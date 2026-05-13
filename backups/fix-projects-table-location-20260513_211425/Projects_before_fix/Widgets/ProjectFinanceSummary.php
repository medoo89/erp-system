<?php

namespace App\Filament\Resources\Projects\Widgets;

use App\Models\Project;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Schema;

class ProjectFinanceSummary extends Widget
{
    protected string $view = 'filament.resources.projects.widgets.project-finance-summary';

    public ?Project $record = null;

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $project = $this->record;

        if (! $project) {
            return ['cards' => []];
        }

        return [
            'cards' => [
                [
                    'label' => 'Salary Cost',
                    'entries' => $this->normalizeEntries($this->salaryTotalsByCurrency($project)),
                    'tone' => 'dark',
                ],
                [
                    'label' => 'Paid Salary',
                    'entries' => $this->normalizeEntries($this->paidSalaryTotalsByCurrency($project)),
                    'tone' => 'info',
                ],
                [
                    'label' => 'Remaining Salary',
                    'entries' => $this->normalizeEntries($this->remainingSalaryTotalsByCurrency($project)),
                    'tone' => 'warning',
                ],
                [
                    'label' => 'Other Expenses',
                    'entries' => $this->normalizeEntries($this->expenseTotalsByCurrency($project)),
                    'tone' => 'neutral',
                ],
                [
                    'label' => 'Revenue Generated',
                    'entries' => $this->normalizeEntries($this->revenueTotalsByCurrency($project)),
                    'tone' => 'success',
                ],
            ],
        ];
    }

    protected function salaryTotalsByCurrency(Project $project): array
    {
        if (! method_exists($project, 'salarySlips')) {
            return [];
        }

        $query = $project->salarySlips();
        $amountColumn = $this->salaryAmountColumn();

        if (! $amountColumn) {
            return [];
        }

        if (! Schema::hasColumn('salary_slips', 'currency')) {
            $amount = round((float) $query->sum($amountColumn), 2);
            return $amount > 0 ? ['EUR' => $amount] : [];
        }

        return $query
            ->selectRaw("currency, SUM({$amountColumn}) as total_amount")
            ->groupBy('currency')
            ->pluck('total_amount', 'currency')
            ->map(fn ($value) => round((float) $value, 2))
            ->filter(fn ($value) => $value > 0)
            ->toArray();
    }

    protected function paidSalaryTotalsByCurrency(Project $project): array
    {
        if (! method_exists($project, 'salarySlips')) {
            return [];
        }

        $query = $project->salarySlips();
        $amountColumn = $this->salaryAmountColumn();

        if (! $amountColumn) {
            return [];
        }

        if (Schema::hasColumn('salary_slips', 'status')) {
            $query->where('status', 'paid');
        }

        if (! Schema::hasColumn('salary_slips', 'currency')) {
            $amount = round((float) $query->sum($amountColumn), 2);
            return $amount > 0 ? ['EUR' => $amount] : [];
        }

        return $query
            ->selectRaw("currency, SUM({$amountColumn}) as total_amount")
            ->groupBy('currency')
            ->pluck('total_amount', 'currency')
            ->map(fn ($value) => round((float) $value, 2))
            ->filter(fn ($value) => $value > 0)
            ->toArray();
    }

    protected function remainingSalaryTotalsByCurrency(Project $project): array
    {
        $all = $this->salaryTotalsByCurrency($project);
        $paid = $this->paidSalaryTotalsByCurrency($project);

        $currencies = array_unique(array_merge(array_keys($all), array_keys($paid)));
        $result = [];

        foreach ($currencies as $currency) {
            $remaining = round((float) ($all[$currency] ?? 0) - (float) ($paid[$currency] ?? 0), 2);

            if ($remaining > 0) {
                $result[$currency] = $remaining;
            }
        }

        return $result;
    }

    protected function expenseTotalsByCurrency(Project $project): array
    {
        if (! method_exists($project, 'financeExpenses')) {
            return [];
        }

        if (! Schema::hasColumn('finance_expenses', 'amount')) {
            return [];
        }

        if (! Schema::hasColumn('finance_expenses', 'currency')) {
            $amount = round((float) $project->financeExpenses()->sum('amount'), 2);
            return $amount > 0 ? ['EUR' => $amount] : [];
        }

        return $project->financeExpenses()
            ->selectRaw('currency, SUM(amount) as total_amount')
            ->groupBy('currency')
            ->pluck('total_amount', 'currency')
            ->map(fn ($value) => round((float) $value, 2))
            ->filter(fn ($value) => $value > 0)
            ->toArray();
    }

    protected function revenueTotalsByCurrency(Project $project): array
    {
        if (! method_exists($project, 'clientInvoiceLines')) {
            return [];
        }

        if (! Schema::hasColumn('client_invoice_lines', 'amount')) {
            return [];
        }

        if (! Schema::hasColumn('client_invoice_lines', 'currency')) {
            $amount = round((float) $project->clientInvoiceLines()->sum('amount'), 2);
            return $amount > 0 ? ['EUR' => $amount] : [];
        }

        return $project->clientInvoiceLines()
            ->selectRaw('currency, SUM(amount) as total_amount')
            ->groupBy('currency')
            ->pluck('total_amount', 'currency')
            ->map(fn ($value) => round((float) $value, 2))
            ->filter(fn ($value) => $value > 0)
            ->toArray();
    }

    protected function salaryAmountColumn(): ?string
    {
        if (Schema::hasColumn('salary_slips', 'total_amount')) {
            return 'total_amount';
        }

        if (Schema::hasColumn('salary_slips', 'net_salary')) {
            return 'net_salary';
        }

        if (Schema::hasColumn('salary_slips', 'amount')) {
            return 'amount';
        }

        return null;
    }

    protected function normalizeEntries(array $totals): array
    {
        if (empty($totals)) {
            return [
                [
                    'currency' => '—',
                    'amount' => '0.00',
                    'empty' => true,
                ],
            ];
        }

        return collect($totals)
            ->map(function ($amount, $currency) {
                return [
                    'currency' => $currency,
                    'amount' => number_format((float) $amount, 2),
                    'empty' => false,
                ];
            })
            ->values()
            ->toArray();
    }
}