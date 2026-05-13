<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'employment_id',
    'name',
    'email',
    'phone',
    'avatar_path',
    'password',
    'user_type',
    'is_admin',
    'portal_status',
    'portal_access_enabled',
    'portal_disabled_reason',
    'portal_disabled_at',
    'password_setup_sent_at',
    'can_manage_admin_settings',
    'erp_permissions',
    'can_view_operations',
    'can_view_hr',
    'can_view_recruitment',
    'can_view_finance',
    'erp_department',
    'erp_role',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    public const TYPE_ADMIN = 'admin';
    public const TYPE_EMPLOYEE_PORTAL = 'employee_portal';

    public const PORTAL_PENDING_PASSWORD_SETUP = 'pending_password_setup';
    public const PORTAL_ACTIVE = 'active';
    public const PORTAL_DISABLED = 'disabled';
    public const PORTAL_BLOCKED = 'blocked';
    public const PORTAL_ARCHIVED = 'archived';


    public function getFilamentName(): string
    {
        return $this->name ?: $this->email;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if (blank($this->avatar_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->avatar_path);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_admin;
    }

    public function employment()
    {
        return $this->belongsTo(Employment::class, 'employment_id');
    }

    public function isEmployeePortalUser(): bool
    {
        return $this->user_type === self::TYPE_EMPLOYEE_PORTAL;
    }

    public function hasEmployeePortalAccess(): bool
    {
        return filled($this->employment_id)
            && (bool) $this->portal_access_enabled
            && in_array($this->portal_status, [
                self::PORTAL_PENDING_PASSWORD_SETUP,
                self::PORTAL_ACTIVE,
            ], true);
    }

    public function isEmployeePortalBlocked(): bool
    {
        return $this->portal_status === self::PORTAL_BLOCKED
            || ! (bool) $this->portal_access_enabled;
    }


    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_admin && ($this->erp_role === 'super_admin' || $this->can_manage_admin_settings);
    }

    public function hasErpRole(string $role): bool
    {
        return $this->isSuperAdmin() || $this->erp_role === $role;
    }

    public function canAccessErpArea(string $area): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        /*
         |--------------------------------------------------------------------------
         | Manual Access Only
         |--------------------------------------------------------------------------
         | Role / Department are labels only.
         | Access must come from the permission matrix checkboxes.
         */

        $areaModules = [
            'finance' => [
                'salary_slips',
                'finance_expenses',
                'treasury',
                'client_invoices',
            ],
            'recruitment' => [
                'jobs',
                'job_applications',
                'pre_employments',
            ],
            'hr' => [
                'employments',
                'pre_employments',
            ],
            'operations' => [
                'clients',
                'projects',
                'travel_tickets',
            ],
            'admin' => [
                'access_control',
                'archive',
                'audit_logs',
            ],
        ];

        foreach (($areaModules[$area] ?? [$area]) as $module) {
            if ($this->canErp($module, 'view')) {
                return true;
            }
        }

        return false;
    }

    public static function erpRoleOptions(): array
    {
        return [
            'super_admin' => 'Super Admin',
            'finance' => 'Finance',
            'recruitment' => 'Recruitment',
            'hr' => 'HR',
            'operations' => 'Operations',
            'viewer' => 'Viewer',
        ];
    }













    public function erpPermissions(): array
    {
        $raw = $this->erp_permissions ?? null;

        if (is_array($raw)) {
            return $raw;
        }

        if (is_string($raw) && trim($raw) !== '') {
            $decoded = json_decode($raw, true);

            if (is_array($decoded)) {
                return $decoded;
            }

            if (is_string($decoded) && trim($decoded) !== '') {
                $decodedAgain = json_decode($decoded, true);

                return is_array($decodedAgain) ? $decodedAgain : [];
            }
        }

        return [];
    }

    public function canErp(string $area, string $action = 'view'): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return (bool) data_get($this->erpPermissions(), "{$area}.{$action}", false);
    }

    public function canAnyErp(string $area, array $actions): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        foreach ($actions as $action) {
            if ($this->canErp($area, $action)) {
                return true;
            }
        }

        return false;
    }

    public function canViewErpModule(string $area): bool
    {
        return $this->canErp($area, 'view');
    }

    public static function erpPermissionRegistry(): array
    {
        return config('erp_permissions.areas', []);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'portal_access_enabled' => 'boolean',
            'portal_disabled_at' => 'datetime',
            'password_setup_sent_at' => 'datetime',
            'can_view_finance' => 'boolean',
            'can_view_recruitment' => 'boolean',
            'can_view_hr' => 'boolean',
            'can_view_operations' => 'boolean',
            'can_manage_admin_settings' => 'boolean',
            'erp_permissions' => 'array',
        ];
    }


    public static function defaultErpPermissionsForRole(?string $role): array
    {
        $role = strtolower(trim((string) $role));
        $registry = static::erpPermissionRegistry();

        $permissions = [];

        foreach ($registry as $module => $config) {
            foreach (($config['actions'] ?? []) as $action => $label) {
                $permissions[$module][$action] = false;
            }
        }

        $grant = function (string $module, array $actions) use (&$permissions): void {
            foreach ($actions as $action) {
                $permissions[$module][$action] = true;
            }
        };

        if ($role === 'super_admin') {
            foreach ($permissions as $module => $actions) {
                foreach ($actions as $action => $value) {
                    $permissions[$module][$action] = true;
                }
            }

            return $permissions;
        }

        if ($role === 'finance') {
            $grant('dashboard', ['view']);
            $grant('employments', ['view']);
            $grant('salary_slips', ['view', 'create', 'edit', 'approve', 'send_to_bank', 'mark_paid', 'upload_attachment', 'update_attendance_days', 'print']);
            $grant('finance_expenses', ['view', 'create', 'edit', 'approve', 'cancel', 'back_to_draft', 'process_payment', 'mark_paid', 'view_treasury_posting', 'upload_attachment']);
            $grant('treasury', ['view', 'create_account', 'edit_account', 'transfer', 'receive', 'pay', 'reconcile', 'view_totals']);
            $grant('treasury_accounts', ['view', 'create', 'edit']);
            $grant('treasury_operations', ['view', 'create', 'edit']);
            $grant('treasury_transactions', ['view', 'create', 'edit']);
            $grant('client_invoices', ['view', 'create', 'edit', 'approve', 'send_to_client', 'record_payment', 'settle_receipts', 'print']);
            $grant('clients', ['view']);
            $grant('projects', ['view', 'generate_salary_slips', 'generate_invoice']);
            $grant('bank_profiles', ['view', 'create', 'edit']);
            $grant('invoice_profiles', ['view', 'create', 'edit']);
            $grant('candidate_finance_profiles', ['view', 'create', 'edit']);

            return $permissions;
        }

        if (in_array($role, ['hr', 'recruitment'], true)) {
            $grant('dashboard', ['view']);
            $grant('jobs', ['view', 'create', 'edit', 'publish', 'close', 'manage_form', 'view_applications']);
            $grant('job_applications', ['view', 'create', 'edit', 'screening', 'hire', 'decline', 'archive', 'send_email', 'create_request', 'delete_request', 'export']);
            $grant('pre_employments', ['view', 'create', 'edit', 'open_public_link', 'send_public_link', 'convert_employment', 'back_to_job_application', 'upload_file', 'delete_file', 'manage_portal_fields', 'send_requirements']);
            $grant('application_templates', ['view', 'create', 'edit', 'manage_fields']);
            $grant('application_fields', ['view', 'create', 'edit']);
            $grant('recruitment_calendar', ['view']);
            $grant('archive', ['view', 'restore']);
            $grant('employments', ['view', 'create', 'edit', 'add_rotation', 'upload_file', 'request_file', 'print_profile', 'print_rotation_history']);
            $grant('clients', ['view']);
            $grant('projects', ['view']);

            return $permissions;
        }

        if ($role === 'viewer') {
            $grant('dashboard', ['view']);
            $grant('employments', ['view']);
            $grant('jobs', ['view']);
            $grant('job_applications', ['view']);
            $grant('pre_employments', ['view']);
            $grant('clients', ['view']);
            $grant('projects', ['view']);

            return $permissions;
        }

        return $permissions;
    }
}
