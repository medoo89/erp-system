<?php

namespace App\Services;

use App\Filament\Resources\SalarySlips\SalarySlipResource;
use App\Filament\Resources\FinanceExpenses\FinanceExpenseResource;
use App\Models\SalarySlip;
use App\Models\FinanceExpense;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminErpNotificationService
{
    /**
     * Main future-ready admin notification method.
     *
     * department/module are ready for the next roles/page-rules stage.
     * Currently it sends to all admin users. Later we will filter by:
     * finance / recruitment / hr / operations / super_admin.
     */
    public function notifyAdmins(
        string $title,
        ?string $body = null,
        string $icon = 'heroicon-o-bell-alert',
        string $color = 'info',
        ?string $url = null,
        string $department = 'system',
        string $module = 'general',
        ?string $relatedType = null,
        ?int $relatedId = null,
    ): void {
        try {
            $admins = $this->resolveAdminUsers($department, $module);

            if ($admins->isEmpty()) {
                return;
            }

            foreach ($admins as $admin) {
                $this->insertFilamentNotification(
                    user: $admin,
                    title: $title,
                    body: $body,
                    icon: $icon,
                    color: $color,
                    url: $url,
                    department: $department,
                    module: $module,
                    relatedType: $relatedType,
                    relatedId: $relatedId,
                );
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function notifySalarySlipEmployeeResponse(
        SalarySlip $salarySlip,
        string $response,
        ?string $employeeNote = null,
    ): void {
        try {
            $salarySlip->loadMissing(['employment', 'client', 'project']);

            $employeeName = $salarySlip->employment?->employee_name
                ?: $salarySlip->employee_name
                ?: 'Employee';

            $period = sprintf(
                '%02d/%04d',
                (int) ($salarySlip->salary_month ?? 0),
                (int) ($salarySlip->salary_year ?? 0)
            );

            $amount = number_format((float) ($salarySlip->net_amount ?? 0), 2) . ' ' . ($salarySlip->currency ?: '');

            $isReceived = $response === 'received';

            $title = $isReceived
                ? 'Employee confirmed salary receipt'
                : 'Employee reported salary not received';

            $bodyParts = [
                'Employee: ' . $employeeName,
                'Period: ' . $period,
                'Amount: ' . $amount,
                'Slip ID: #' . $salarySlip->getKey(),
            ];

            if (filled($employeeNote)) {
                $bodyParts[] = 'Employee note: ' . $employeeNote;
            }

            $this->notifyAdmins(
                title: $title,
                body: implode(' · ', array_filter($bodyParts)),
                icon: $isReceived ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-triangle',
                color: $isReceived ? 'success' : 'danger',
                url: $this->resolveSalarySlipUrl($salarySlip),
                department: 'finance',
                module: 'salary_slips',
                relatedType: SalarySlip::class,
                relatedId: (int) $salarySlip->getKey(),
            );
        } catch (\Throwable $e) {
            report($e);
        }
    }


    public function notifyReimbursementClaimSubmitted(FinanceExpense $expense, string $source = 'employee_portal'): void
    {
        try {
            $expense->loadMissing(['employment', 'preEmployment', 'client', 'project']);

            $owner = $expense->ownerName() ?: 'Employee / Candidate';
            $amount = number_format((float) ($expense->reimbursement_amount ?: $expense->amount ?: 0), 2)
                . ' '
                . ($expense->reimbursement_currency ?: $expense->currency ?: '');

            $sourceLabel = $source === 'pre_employment_portal'
                ? 'Pre-Employment Portal'
                : 'Employee Portal';

            $bodyParts = [
                'Submitted from: ' . $sourceLabel,
                'Owner: ' . $owner,
                'Claim: ' . ($expense->title ?: 'Reimbursement Claim'),
                'Amount: ' . $amount,
                'Status: Pending',
                'Expense ID: #' . $expense->getKey(),
            ];

            $this->notifyAdmins(
                title: 'New reimbursement claim submitted',
                body: implode(' · ', array_filter($bodyParts)),
                icon: 'heroicon-o-receipt-refund',
                color: 'warning',
                url: $this->resolveFinanceExpenseUrl($expense),
                department: 'finance',
                module: 'finance_expenses',
                relatedType: FinanceExpense::class,
                relatedId: (int) $expense->getKey(),
            );
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function notifyFileEvent(
        string $title,
        ?string $body = null,
        ?string $url = null,
        string $department = 'hr',
        string $module = 'files',
        ?string $relatedType = null,
        ?int $relatedId = null,
    ): void {
        $this->notifyAdmins(
            title: $title,
            body: $body,
            icon: 'heroicon-o-document-text',
            color: 'info',
            url: $url,
            department: $department,
            module: $module,
            relatedType: $relatedType,
            relatedId: $relatedId,
        );
    }

    public function notifyRotationEvent(
        string $title,
        ?string $body = null,
        ?string $url = null,
        ?string $relatedType = null,
        ?int $relatedId = null,
    ): void {
        $this->notifyAdmins(
            title: $title,
            body: $body,
            icon: 'heroicon-o-arrow-path-rounded-square',
            color: 'warning',
            url: $url,
            department: 'operations',
            module: 'rotations',
            relatedType: $relatedType,
            relatedId: $relatedId,
        );
    }

    public function notifyTravelEvent(
        string $title,
        ?string $body = null,
        ?string $url = null,
        ?string $relatedType = null,
        ?int $relatedId = null,
    ): void {
        $this->notifyAdmins(
            title: $title,
            body: $body,
            icon: 'heroicon-o-paper-airplane',
            color: 'info',
            url: $url,
            department: 'operations',
            module: 'travel',
            relatedType: $relatedType,
            relatedId: $relatedId,
        );
    }

    protected function insertFilamentNotification(
        User $user,
        string $title,
        ?string $body = null,
        string $icon = 'heroicon-o-bell-alert',
        string $color = 'info',
        ?string $url = null,
        string $department = 'system',
        string $module = 'general',
        ?string $relatedType = null,
        ?int $relatedId = null,
    ): void {
        $actions = [];

        if ($url) {
            $actions[] = [
                'name' => 'open',
                'label' => 'Open',
                'url' => $url,
                'color' => 'primary',
                'shouldOpenUrlInNewTab' => false,
                'shouldMarkAsRead' => true,
            ];
        }

        DB::table('notifications')->insert([
            'id' => (string) Str::uuid(),
            'type' => 'Filament\\Notifications\\DatabaseNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => json_encode([
                'title' => $title,
                'body' => $body,
                'icon' => $icon,
                'iconColor' => $color,
                'color' => $color,
                'status' => $color,
                'actions' => $actions,
                'duration' => 'persistent',
                'format' => 'filament',
                'view' => 'filament-notifications::notification',
                'viewData' => [],
                'sada' => [
                    'department' => $department,
                    'module' => $module,
                    'related_type' => $relatedType,
                    'related_id' => $relatedId,
                ],
            ]),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function resolveSalarySlipUrl(SalarySlip $salarySlip): string
    {
        try {
            return SalarySlipResource::getUrl('view', [
                'record' => $salarySlip,
            ]);
        } catch (\Throwable $e) {
            return url('/admin/salary-slips/' . $salarySlip->getKey());
        }
    }


    protected function resolveFinanceExpenseUrl(FinanceExpense $expense): string
    {
        try {
            return FinanceExpenseResource::getUrl('view', [
                'record' => $expense,
            ]);
        } catch (\Throwable $e) {
            return url('/admin/finance-expenses/' . $expense->getKey());
        }
    }

    /**
     * Future-ready resolver.
     * Later, this will filter by roles/permissions.
     */
    protected function resolveAdminUsers(string $department = 'system', string $module = 'general'): Collection
    {
        $query = User::query();

        if (Schema::hasColumn('users', 'is_admin')) {
            $query->where('is_admin', true);
        } elseif (Schema::hasColumn('users', 'user_type')) {
            $query->whereIn('user_type', ['admin', 'super_admin']);
        }

        $users = $query->get();

        if ($department !== 'finance' && $module !== 'finance_expenses') {
            return $users;
        }

        $financeUsers = $users->filter(function (User $user): bool {
            if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
                return true;
            }

            if (method_exists($user, 'canErp')) {
                return $user->canErp('finance_expenses', 'view')
                    || $user->canErp('finance_expenses', 'approve')
                    || $user->canErp('finance_expenses', 'edit')
                    || $user->canErp('treasury', 'view');
            }

            return (string) ($user->erp_role ?? '') === 'finance'
                || (string) ($user->erp_department ?? '') === 'finance'
                || (bool) ($user->can_view_finance ?? false);
        })->values();

        return $financeUsers->isNotEmpty() ? $financeUsers : $users;
    }
}
