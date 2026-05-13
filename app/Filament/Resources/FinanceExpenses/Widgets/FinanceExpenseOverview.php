<?php

namespace App\Filament\Resources\FinanceExpenses\Widgets;

use App\Models\FinanceExpense;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinanceExpenseOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalExpenses = FinanceExpense::query()->count();

        $draftCount = FinanceExpense::query()
            ->where('status', FinanceExpense::STATUS_DRAFT)
            ->count();

        $approvedCount = FinanceExpense::query()
            ->where('status', FinanceExpense::STATUS_APPROVED)
            ->count();

        $paidCount = FinanceExpense::query()
            ->where('status', FinanceExpense::STATUS_PAID)
            ->count();

        $companyPaidCount = FinanceExpense::query()
            ->where('paid_by', FinanceExpense::PAID_BY_COMPANY)
            ->count();

        $pendingReimbursementCount = FinanceExpense::query()
            ->where('reimbursement_status', FinanceExpense::REIMBURSEMENT_PENDING)
            ->count();

        return [
            Stat::make('Total Expenses', number_format($totalExpenses))
                ->description('All finance expense records')
                ->color('primary'),

            Stat::make('Draft', number_format($draftCount))
                ->description('Still under preparation')
                ->color('gray'),

            Stat::make('Approved', number_format($approvedCount))
                ->description('Approved expense records')
                ->color('info'),

            Stat::make('Paid', number_format($paidCount))
                ->description('Fully paid expenses')
                ->color('success'),

            Stat::make('Company Paid', number_format($companyPaidCount))
                ->description('Expenses paid by company')
                ->color('success'),

            Stat::make('Pending Reimbursement', number_format($pendingReimbursementCount))
                ->description('Awaiting reimbursement action')
                ->color('warning'),
        ];
    }
}
