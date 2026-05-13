<?php

namespace App\Services;

use App\Models\FinanceExpense;
use Illuminate\Database\Eloquent\Builder;

class FinanceSummaryService
{
    public function expenseQuery(array $filters = []): Builder
    {
        $query = FinanceExpense::query()
            ->with([
                'jobApplication',
                'employment',
                'client',
                'project',
                'financeProfile',
            ])
            ->active();

        $query->forCandidate($filters['job_application_id'] ?? null);
        $query->forEmployment($filters['employment_id'] ?? null);
        $query->forClient($filters['client_id'] ?? null);
        $query->forProject($filters['project_id'] ?? null);
        $query->forScope($filters['expense_scope'] ?? null);
        $query->forCategory($filters['category'] ?? null);
        $query->forCurrency($filters['currency'] ?? null);
        $query->betweenDates(
            $filters['date_from'] ?? null,
            $filters['date_to'] ?? null
        );

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['paid_by'])) {
            $query->where('paid_by', $filters['paid_by']);
        }

        if (array_key_exists('is_first_mobilization', $filters) && $filters['is_first_mobilization'] !== null) {
            $query->where('is_first_mobilization', (bool) $filters['is_first_mobilization']);
        }

        return $query;
    }

    public function totalsByCurrency(array $filters = []): array
    {
        return FinanceExpense::totalsByCurrency(
            $this->expenseQuery($filters)
        );
    }

    public function grandExpenseSummary(array $filters = []): array
    {
        $totals = $this->totalsByCurrency($filters);

        return [
            'total_records' => (clone $this->expenseQuery($filters))->count(),
            'totals_by_currency' => $totals,
            'usd_total' => (float) ($totals['USD'] ?? 0),
            'eur_total' => (float) ($totals['EUR'] ?? 0),
            'gbp_total' => (float) ($totals['GBP'] ?? 0),
            'lyd_total' => (float) ($totals['LYD'] ?? 0),
        ];
    }

    public function totalsByCandidate(array $filters = []): array
    {
        $rows = (clone $this->expenseQuery($filters))
            ->selectRaw('job_application_id, currency, SUM(amount) as total_amount')
            ->groupBy('job_application_id', 'currency')
            ->get();

        $grouped = [];

        foreach ($rows as $row) {
            $jobApplicationId = (int) $row->job_application_id;

            if (! isset($grouped[$jobApplicationId])) {
                $grouped[$jobApplicationId] = [
                    'job_application_id' => $jobApplicationId,
                    'USD' => 0.0,
                    'EUR' => 0.0,
                    'GBP' => 0.0,
                    'LYD' => 0.0,
                ];
            }

            $currency = strtoupper((string) $row->currency);

            if (array_key_exists($currency, $grouped[$jobApplicationId])) {
                $grouped[$jobApplicationId][$currency] = (float) $row->total_amount;
            }
        }

        return array_values($grouped);
    }

    public function totalsByClient(array $filters = []): array
    {
        $rows = (clone $this->expenseQuery($filters))
            ->selectRaw('client_id, currency, SUM(amount) as total_amount')
            ->groupBy('client_id', 'currency')
            ->get();

        $grouped = [];

        foreach ($rows as $row) {
            $clientId = (int) $row->client_id;

            if (! isset($grouped[$clientId])) {
                $grouped[$clientId] = [
                    'client_id' => $clientId,
                    'USD' => 0.0,
                    'EUR' => 0.0,
                    'GBP' => 0.0,
                    'LYD' => 0.0,
                ];
            }

            $currency = strtoupper((string) $row->currency);

            if (array_key_exists($currency, $grouped[$clientId])) {
                $grouped[$clientId][$currency] = (float) $row->total_amount;
            }
        }

        return array_values($grouped);
    }

    public function totalsByProject(array $filters = []): array
    {
        $rows = (clone $this->expenseQuery($filters))
            ->selectRaw('project_id, currency, SUM(amount) as total_amount')
            ->groupBy('project_id', 'currency')
            ->get();

        $grouped = [];

        foreach ($rows as $row) {
            $projectId = (int) $row->project_id;

            if (! isset($grouped[$projectId])) {
                $grouped[$projectId] = [
                    'project_id' => $projectId,
                    'USD' => 0.0,
                    'EUR' => 0.0,
                    'GBP' => 0.0,
                    'LYD' => 0.0,
                ];
            }

            $currency = strtoupper((string) $row->currency);

            if (array_key_exists($currency, $grouped[$projectId])) {
                $grouped[$projectId][$currency] = (float) $row->total_amount;
            }
        }

        return array_values($grouped);
    }

    public function totalsByCategory(array $filters = []): array
    {
        $rows = (clone $this->expenseQuery($filters))
            ->selectRaw('category, currency, SUM(amount) as total_amount')
            ->groupBy('category', 'currency')
            ->get();

        $grouped = [];

        foreach ($rows as $row) {
            $category = (string) $row->category;

            if (! isset($grouped[$category])) {
                $grouped[$category] = [
                    'category' => $category,
                    'USD' => 0.0,
                    'EUR' => 0.0,
                    'GBP' => 0.0,
                    'LYD' => 0.0,
                ];
            }

            $currency = strtoupper((string) $row->currency);

            if (array_key_exists($currency, $grouped[$category])) {
                $grouped[$category][$currency] = (float) $row->total_amount;
            }
        }

        return array_values($grouped);
    }

    public function totalsByMonth(array $filters = []): array
    {
        $rows = (clone $this->expenseQuery($filters))
            ->selectRaw("strftime('%Y-%m', expense_date) as expense_month, currency, SUM(amount) as total_amount")
            ->whereNotNull('expense_date')
            ->groupBy('expense_month', 'currency')
            ->orderBy('expense_month')
            ->get();

        $grouped = [];

        foreach ($rows as $row) {
            $month = (string) $row->expense_month;

            if (! isset($grouped[$month])) {
                $grouped[$month] = [
                    'month' => $month,
                    'USD' => 0.0,
                    'EUR' => 0.0,
                    'GBP' => 0.0,
                    'LYD' => 0.0,
                ];
            }

            $currency = strtoupper((string) $row->currency);

            if (array_key_exists($currency, $grouped[$month])) {
                $grouped[$month][$currency] = (float) $row->total_amount;
            }
        }

        return array_values($grouped);
    }

    public function detailedSchedule(array $filters = [])
    {
        return $this->expenseQuery($filters)
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->get();
    }

    public function candidateSummary(int $jobApplicationId, array $filters = []): array
    {
        $filters['job_application_id'] = $jobApplicationId;

        return [
            'summary' => $this->grandExpenseSummary($filters),
            'by_category' => $this->totalsByCategory($filters),
            'by_month' => $this->totalsByMonth($filters),
            'details' => $this->detailedSchedule($filters),
        ];
    }

    public function clientSummary(int $clientId, array $filters = []): array
    {
        $filters['client_id'] = $clientId;

        return [
            'summary' => $this->grandExpenseSummary($filters),
            'by_project' => $this->totalsByProject($filters),
            'by_category' => $this->totalsByCategory($filters),
            'by_month' => $this->totalsByMonth($filters),
            'details' => $this->detailedSchedule($filters),
        ];
    }

    public function projectSummary(int $projectId, array $filters = []): array
    {
        $filters['project_id'] = $projectId;

        return [
            'summary' => $this->grandExpenseSummary($filters),
            'by_candidate' => $this->totalsByCandidate($filters),
            'by_category' => $this->totalsByCategory($filters),
            'by_month' => $this->totalsByMonth($filters),
            'details' => $this->detailedSchedule($filters),
        ];
    }
}