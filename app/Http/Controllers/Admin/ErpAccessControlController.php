<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AuditLog;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ErpAccessControlController extends Controller
{

    protected function canAccessControl(string $action = 'view'): bool
    {
        $user = auth()->user();

        return (bool) (
            $user?->isSuperAdmin()
            || $user?->canErp('access_control', $action)
        );
    }

    protected function ensureAccessControl(string $action = 'view'): void
    {
        abort_unless($this->canAccessControl($action), 403);
    }

    protected function isLastSuperAdmin(User $user): bool
    {
        if (! $user->isSuperAdmin()) {
            return false;
        }

        return User::query()
            ->where('is_admin', true)
            ->where('erp_role', 'super_admin')
            ->count() <= 1;
    }

    public function index()
    {
        $this->ensureAccessControl('view');

        $users = User::query()
            ->orderByDesc('is_admin')
            ->orderByRaw("COALESCE(erp_department, '') asc")
            ->orderByRaw("COALESCE(erp_role, '') asc")
            ->orderBy('name')
            ->get();

        return view('admin.access-control.index', [
            'users' => $users,
            'roles' => User::erpRoleOptions(),
            'registry' => User::erpPermissionRegistry(),
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureAccessControl('create_user');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'erp_role' => ['required', 'string', 'max:255'],
            'erp_department' => ['nullable', 'string', 'max:255'],
            'is_admin' => ['nullable'],
        ]);

        $payload = [
            'name' => $validated['name'],
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'is_admin' => (bool) ($validated['is_admin'] ?? true),
            'user_type' => User::TYPE_ADMIN,
            'erp_role' => $validated['erp_role'],
            'erp_department' => $validated['erp_department'] ?? null,
            'erp_permissions' => json_encode(User::defaultErpPermissionsForRole($validated['erp_role']), JSON_UNESCAPED_UNICODE),
        ];

        if (Schema::hasColumn('users', 'phone')) {
            $payload['phone'] = $validated['phone'] ?? null;
        }

        $createdUser = User::query()->create($payload);

        AuditLogService::created('page_rules', $createdUser, 'Created ERP user from Page Rules', [
            'created_email' => $createdUser->email,
            'created_role' => $createdUser->erp_role,
            'created_department' => $createdUser->erp_department,
        ]);

        return redirect()
            ->route('admin.erp-access-control.index')
            ->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $this->ensureAccessControl('manage_permissions');

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'erp_role' => ['required', 'string', 'max:255'],
            'erp_department' => ['nullable', 'string', 'max:255'],
            'is_admin' => ['nullable'],
            'permissions' => ['nullable', 'array'],
            'new_password' => ['nullable', 'string', 'min:8'],
        ]);

        $permissions = [];
        $registry = User::erpPermissionRegistry();

        foreach ($registry as $area => $module) {
            foreach (($module['actions'] ?? []) as $action => $label) {
                $permissions[$area][$action] = (bool) data_get($validated, "permissions.{$area}.{$action}", false);
            }
        }

        $payload = [
            'name' => $validated['name'] ?? $user->name,
            'erp_role' => $validated['erp_role'],
            'erp_department' => $validated['erp_department'] ?? null,
            'is_admin' => (bool) ($validated['is_admin'] ?? false),
            'user_type' => (bool) ($validated['is_admin'] ?? false) ? User::TYPE_ADMIN : ($user->user_type ?: User::TYPE_EMPLOYEE_PORTAL),
            'erp_permissions' => json_encode($permissions, JSON_UNESCAPED_UNICODE),
        ];

        if (! empty($validated['new_password'])) {
            $payload['password'] = Hash::make($validated['new_password']);
        }

        if (array_key_exists('phone', $validated) && Schema::hasColumn('users', 'phone')) {
            $payload['phone'] = $validated['phone'];
        }

        $oldValues = $user->only(['name', 'erp_role', 'erp_department', 'is_admin', 'user_type', 'erp_permissions']);

        $user->forceFill($payload)->save();

        AuditLogService::updated('page_rules', $user, $oldValues, $user->fresh()->only(['name', 'erp_role', 'erp_department', 'is_admin', 'user_type', 'erp_permissions']), 'Updated ERP page rules for user', [
            'target_user_email' => $user->email,
        ]);

        return redirect()
            ->route('admin.erp-access-control.index')
            ->with('success', 'Access rules updated for ' . $user->email)
            ->with('selected_user_id', $user->id);
    }

    public function destroy(User $user)
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

        if ((int) auth()->id() === (int) $user->id) {
            return redirect()
                ->route('admin.erp-access-control.index')
                ->withErrors(['user' => 'You cannot delete your own logged-in user.']);
        }

        $actor = auth()->user();

        $deletedUserSnapshot = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'erp_role' => $user->erp_role,
            'erp_department' => $user->erp_department,
            'is_admin' => $user->is_admin,
            'user_type' => $user->user_type,
        ];

        $email = $user->email;
        $name = $user->name;

        /*
         |--------------------------------------------------------------------------
         | Important:
         |--------------------------------------------------------------------------
         | Log the delete as a direct audit row before deleting the user.
         | Do not depend on morph subject relation because the target user will be deleted.
         */
        AuditLog::query()->create([
            'user_id' => $actor?->id,
            'user_name' => $actor?->name,
            'user_email' => $actor?->email,
            'user_role' => $actor?->erp_role,
            'user_department' => $actor?->erp_department,

            'action' => 'delete',
            'module' => 'page_rules',
            'module_label' => 'Page Rules',

            'subject_type' => User::class,
            'subject_id' => $user->id,
            'subject_title' => $name ?: $email,
            'subject_reference' => $email,

            'severity' => 'danger',
            'status' => 'success',
            'description' => 'Deleted ERP user from Page Rules.',

            'old_values' => $deletedUserSnapshot,
            'new_values' => null,
            'meta' => [
                'deleted_user_email' => $email,
                'deleted_user_name' => $name,
                'source' => 'page_rules_delete_user',
            ],

            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'method' => request()->method(),
            'url' => request()->fullUrl(),
            'route_name' => request()->route()?->getName(),

            'performed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user->delete();

        return redirect()
            ->route('admin.erp-access-control.index')
            ->with('success', 'User permanently deleted: ' . $email);
    }
}
