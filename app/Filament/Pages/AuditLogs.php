<?php

namespace App\Filament\Pages;

use App\Models\AuditLog;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class AuditLogs extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Admin Settings';

    protected static ?string $navigationLabel = 'Audit Logs';

    protected static ?string $title = 'Audit Logs';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?int $navigationSort = 40;

    protected static ?string $slug = 'audit-logs';

    protected static bool $shouldRegisterNavigation = true;

    protected string $view = 'filament.pages.audit-logs';

    public ?string $search = null;

    public ?string $userId = null;

    public ?string $module = null;

    public ?string $action = null;

    public ?string $role = null;

    public ?string $department = null;

    public ?string $severity = null;

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public ?string $ip = null;

    public ?string $dangerOnly = null;

    public function getTitle(): string|Htmlable
    {
        return '';
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return (bool) (
            auth()->user()?->isSuperAdmin()
            || auth()->user()?->canErp('audit_logs', 'view')
        );
    }

    public function resetFilters(): void
    {
        $this->search = null;
        $this->userId = null;
        $this->module = null;
        $this->action = null;
        $this->role = null;
        $this->department = null;
        $this->severity = null;
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->ip = null;
        $this->dangerOnly = null;
    }

    public function getViewData(): array
    {
        $query = AuditLog::query()
            ->with('user')
            ->orderByDesc('performed_at')
            ->orderByDesc('id');

        if (filled($this->search)) {
            $term = trim($this->search);

            $query->where(function ($q) use ($term) {
                $q->where('description', 'like', "%{$term}%")
                    ->orWhere('user_name', 'like', "%{$term}%")
                    ->orWhere('user_email', 'like', "%{$term}%")
                    ->orWhere('module', 'like', "%{$term}%")
                    ->orWhere('action', 'like', "%{$term}%")
                    ->orWhere('subject_title', 'like', "%{$term}%")
                    ->orWhere('subject_reference', 'like', "%{$term}%")
                    ->orWhere('ip_address', 'like', "%{$term}%");
            });
        }

        if (filled($this->userId)) {
            $query->where('user_id', $this->userId);
        }

        if (filled($this->module)) {
            $query->where('module', $this->module);
        }

        if (filled($this->action)) {
            $query->where('action', $this->action);
        }

        if (filled($this->role)) {
            $query->where('user_role', $this->role);
        }

        if (filled($this->department)) {
            $query->where('user_department', 'like', '%' . trim($this->department) . '%');
        }

        if (filled($this->severity)) {
            $query->where('severity', $this->severity);
        }

        if (filled($this->ip)) {
            $query->where('ip_address', 'like', '%' . trim($this->ip) . '%');
        }

        if (filled($this->dateFrom)) {
            $query->whereDate('performed_at', '>=', $this->dateFrom);
        }

        if (filled($this->dateTo)) {
            $query->whereDate('performed_at', '<=', $this->dateTo);
        }

        if ($this->dangerOnly === '1') {
            $query->whereIn('action', [
                'delete',
                'disable',
                'enable',
                'approve',
                'send',
                'print',
                'security',
                'update',
            ]);
        }

        $logs = $query->paginate(25);

        return [
            'logs' => $logs,
            'users' => User::query()
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
            'modules' => AuditLog::query()
                ->select('module')
                ->whereNotNull('module')
                ->distinct()
                ->orderBy('module')
                ->pluck('module')
                ->toArray(),
            'actions' => AuditLog::query()
                ->select('action')
                ->whereNotNull('action')
                ->distinct()
                ->orderBy('action')
                ->pluck('action')
                ->toArray(),
            'roles' => User::erpRoleOptions(),
        ];
    }
}
