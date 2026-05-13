<?php

namespace App\Filament\Resources\Employments\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmploymentFinanceSummary extends StatsOverviewWidget
{
    public ?object $record = null;

    protected function getStats(): array
    {
        $record = $this->record;

        if (! $record) {
            return [];
        }

        $dailyRate = (float) ($record->resolvedDailyRate() ?? 0);
        $dailyRateCurrency = $record->resolvedSalaryCurrency() ?: '-';

        $billingRate = (float) ($record->resolvedClientBillingRate() ?? 0);
        $billingCurrency = $record->resolvedClientBillingCurrency() ?: '-';

        $salaryCost = (float) ($record->totalSalaryCost() ?? 0);
        $salaryCostCurrency = $record->resolvedSalaryCurrency() ?: '-';

        $paidSalaryCost = (float) ($record->paidSalaryCost() ?? 0);
        $remainingSalaryCost = (float) ($record->remainingSalaryCost() ?? 0);

        $totalRevenue = (float) ($record->totalRevenueGenerated() ?? 0);
        $otherExpenses = (float) ($record->totalOtherExpenses() ?? 0);
        $netResult = (float) ($record->netResult() ?? 0);

        $foreignRevenue = $record->revenueForeignByCurrency();
        $localRevenue = $record->revenueLocalByCurrency();

        $foreignRevenueText = ! empty($foreignRevenue)
            ? collect($foreignRevenue)
                ->map(fn ($amount, $currency) => number_format((float) $amount, 2) . ' ' . $currency)
                ->implode(' • ')
            : 'No foreign revenue yet';

        $localRevenueText = ! empty($localRevenue)
            ? collect($localRevenue)
                ->map(fn ($amount, $currency) => number_format((float) $amount, 2) . ' ' . $currency)
                ->implode(' • ')
            : 'No local revenue yet';

        return [
            Stat::make('Daily Rate', number_format($dailyRate, 2) . ' ' . $dailyRateCurrency)
                ->description('Employee payout rate')
                ->color('info'),

            Stat::make('Client Billing Rate', number_format($billingRate, 2) . ' ' . $billingCurrency)
                ->description('Client-facing billing rate')
                ->color('success'),

            Stat::make('Salary Cost', number_format($salaryCost, 2) . ' ' . $salaryCostCurrency)
                ->description(
                    'Paid: ' . number_format($paidSalaryCost, 2) . ' ' . $salaryCostCurrency
                    . ' • Remaining: ' . number_format($remainingSalaryCost, 2) . ' ' . $salaryCostCurrency
                )
                ->color('warning'),

            Stat::make('Revenue', number_format($totalRevenue, 2))
                ->description($foreignRevenueText . ' | ' . $localRevenueText)
                ->color('primary'),

            Stat::make('Other Expenses', number_format($otherExpenses, 2))
                ->description('Employment-linked extra costs')
                ->color('gray'),

            Stat::make('Net Result', number_format($netResult, 2))
                ->description('Revenue - Salary Cost - Other Expenses')
                ->color($netResult >= 0 ? 'success' : 'danger'),
        ];
    }
}