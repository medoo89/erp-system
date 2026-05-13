<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FinanceTotalsService
{
    public function build(array $filters = []): array
    {
        [$startDate, $endDate] = $this->resolveDateRange($filters);

        $projectId = ! empty($filters['project_id']) ? (int) $filters['project_id'] : null;
        $clientId = ! empty($filters['client_id']) ? (int) $filters['client_id'] : null;
        $employmentId = ! empty($filters['employment_id']) ? (int) $filters['employment_id'] : null;

        return [
            'range' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
            ],

            'summary' => $this->buildSummary($startDate, $endDate, $projectId, $clientId, $employmentId),

            'by_currency' => $this->buildCurrencyBreakdown($startDate, $endDate, $projectId, $clientId, $employmentId),

            'by_client' => $this->buildClientBreakdown($startDate, $endDate, $projectId, $clientId, $employmentId),

            'by_project' => $this->buildProjectBreakdown($startDate, $endDate, $projectId, $clientId, $employmentId),

            'by_employee' => $this->buildEmployeeBreakdown($startDate, $endDate, $projectId, $clientId, $employmentId),
        ];
    }

    protected function buildSummary(
        Carbon $startDate,
        Carbon $endDate,
        ?int $projectId,
        ?int $clientId,
        ?int $employmentId
    ): array {
        $revenueByCurrency = $this->invoiceRevenueByCurrency($startDate, $endDate, $projectId, $clientId, $employmentId);

        $salaryCostByCurrency = $this->salaryAmountsByCurrency(
            $this->salarySlipsStatusBaseQuery($startDate, $endDate, $projectId, $clientId, $employmentId, ['sent_to_bank', 'paid'])
        );

        $salaryPaidByCurrency = $this->salaryAmountsByCurrency(
            $this->salarySlipsStatusBaseQuery($startDate, $endDate, $projectId, $clientId, $employmentId, ['sent_to_bank', 'paid'])
        );

        $salaryRemainingByCurrency = $this->subtractCurrencyMaps(
            $salaryCostByCurrency,
            $salaryPaidByCurrency,
            floorAtZero: true,
        );

        $salaryApprovedByCurrency = $this->salaryAmountsByCurrency(
            $this->salarySlipsStatusBaseQuery($startDate, $endDate, $projectId, $clientId, $employmentId, ['approved'])
        );

        $salaryDraftByCurrency = $this->salaryAmountsByCurrency(
            $this->salarySlipsStatusBaseQuery($startDate, $endDate, $projectId, $clientId, $employmentId, ['draft'])
        );

        $expensesByCurrency = $this->expenseAmountsByCurrency(
            $this->expensesBaseQuery($startDate, $endDate, $projectId, $clientId, $employmentId)
        );

        $netByCurrency = $this->subtractCurrencyMaps(
            $this->subtractCurrencyMaps($revenueByCurrency, $salaryCostByCurrency),
            $expensesByCurrency
        );

        return [
            'revenue_by_currency' => $this->normalizeCurrencyMap($revenueByCurrency),
            'salary_cost_by_currency' => $this->normalizeCurrencyMap($salaryCostByCurrency),
            'salary_paid_by_currency' => $this->normalizeCurrencyMap($salaryPaidByCurrency),
            'salary_remaining_by_currency' => $this->normalizeCurrencyMap($salaryRemainingByCurrency),
            'salary_approved_by_currency' => $this->normalizeCurrencyMap($salaryApprovedByCurrency),
            'salary_draft_by_currency' => $this->normalizeCurrencyMap($salaryDraftByCurrency),
            'expenses_by_currency' => $this->normalizeCurrencyMap($expensesByCurrency),
            'net_by_currency' => $this->normalizeCurrencyMap($netByCurrency),
        ];
    }

    protected function buildCurrencyBreakdown(
        Carbon $startDate,
        Carbon $endDate,
        ?int $projectId,
        ?int $clientId,
        ?int $employmentId
    ): array {
        $summary = $this->buildSummary($startDate, $endDate, $projectId, $clientId, $employmentId);

        $revenue = $summary['revenue_by_currency'] ?? [];
        $salary = $summary['salary_cost_by_currency'] ?? [];
        $expenses = $summary['expenses_by_currency'] ?? [];
        $net = $summary['net_by_currency'] ?? [];

        $currencies = $this->collectAllCurrencies($revenue, $salary, $expenses, $net);

        return collect($currencies)
            ->map(function (string $currency) use ($revenue, $salary, $expenses, $net) {
                return [
                    'currency' => $currency,
                    'revenue_total' => round((float) ($revenue[$currency] ?? 0), 2),
                    'salary_cost_total' => round((float) ($salary[$currency] ?? 0), 2),
                    'expenses_total' => round((float) ($expenses[$currency] ?? 0), 2),
                    'net_result_total' => round((float) ($net[$currency] ?? 0), 2),
                ];
            })
            ->values()
            ->all();
    }

    protected function buildProjectBreakdown(
        Carbon $startDate,
        Carbon $endDate,
        ?int $projectId,
        ?int $clientId,
        ?int $employmentId
    ): array {
        $revenue = $this->projectRevenueBuckets($startDate, $endDate, $projectId, $clientId, $employmentId);
        $salary = $this->projectSalaryBuckets($startDate, $endDate, $projectId, $clientId, $employmentId);
        $expenses = $this->projectExpenseBuckets($startDate, $endDate, $projectId, $clientId, $employmentId);

        return $this->mergeEntityBuckets($revenue, $salary, $expenses, 'project_id', 'project_name');
    }

    protected function buildEmployeeBreakdown(
        Carbon $startDate,
        Carbon $endDate,
        ?int $projectId,
        ?int $clientId,
        ?int $employmentId
    ): array {
        $revenue = $this->employeeRevenueBuckets($startDate, $endDate, $projectId, $clientId, $employmentId);
        $salary = $this->employeeSalaryBuckets($startDate, $endDate, $projectId, $clientId, $employmentId);
        $expenses = $this->employeeExpenseBuckets($startDate, $endDate, $projectId, $clientId, $employmentId);

        $rows = $this->mergeEntityBuckets($revenue, $salary, $expenses, 'employment_id', 'employee_name');

        return collect($rows)
            ->map(function (array $row) use ($revenue, $salary, $expenses) {
                $id = $row['employment_id'];

                $row['position_title'] = $revenue[$id]['position_title']
                    ?? ($salary[$id]['position_title'] ?? ($expenses[$id]['position_title'] ?? '-'));

                return $row;
            })
            ->values()
            ->all();
    }

    protected function buildClientBreakdown(
        Carbon $startDate,
        Carbon $endDate,
        ?int $projectId,
        ?int $clientId,
        ?int $employmentId
    ): array {
        $revenue = $this->clientRevenueBuckets($startDate, $endDate, $projectId, $clientId, $employmentId);
        $salary = $this->clientSalaryBuckets($startDate, $endDate, $projectId, $clientId, $employmentId);
        $expenses = $this->clientExpenseBuckets($startDate, $endDate, $projectId, $clientId, $employmentId);

        return $this->mergeEntityBuckets($revenue, $salary, $expenses, 'client_id', 'client_name');
    }

    protected function invoicePaymentsBaseQuery(
        Carbon $startDate,
        Carbon $endDate,
        ?int $projectId,
        ?int $clientId,
        ?int $employmentId
    ) {
        /*
         * Critical launch rule:
         * Global revenue must come from actual collectors only.
         * Do NOT count invoice lines, invoice totals, approved invoices, sent invoices,
         * or unpaid invoices as revenue.
         */
        $query = DB::table('client_invoice_payments as cip')
            ->join('client_invoices as ci', 'ci.id', '=', 'cip.client_invoice_id')
            ->where('cip.settlement_status', 'cleared')
            ->whereBetween('cip.payment_date', [$startDate->toDateString(), $endDate->toDateString()]);

        if ($projectId) {
            $query->where('ci.project_id', $projectId);
        }

        if ($clientId) {
            $query->where('ci.client_id', $clientId);
        }

        if ($employmentId) {
            $query->whereExists(function ($sub) use ($employmentId) {
                $sub->from('client_invoice_lines as cilx')
                    ->whereColumn('cilx.client_invoice_id', 'ci.id')
                    ->where('cilx.employment_id', $employmentId);
            });
        }

        return $query;
    }

    protected function invoiceRevenueByCurrency(
        Carbon $startDate,
        Carbon $endDate,
        ?int $projectId,
        ?int $clientId,
        ?int $employmentId
    ): array {
        /*
         * Launch-safe rule:
         * Revenue = actual collected receipts only.
         * Source: client_invoice_payments.
         * Count only cleared receipts.
         * Do not count invoice lines / approved invoices / sent invoices.
         */
        $query = DB::table('client_invoice_payments as cip')
            ->join('client_invoices as ci', 'ci.id', '=', 'cip.client_invoice_id')
            ->whereRaw("LOWER(COALESCE(cip.settlement_status, '')) = 'cleared'")
            ->whereBetween(DB::raw('DATE(cip.payment_date)'), [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ]);

        if ($projectId) {
            $query->where('ci.project_id', $projectId);
        }

        if ($clientId) {
            $query->where('ci.client_id', $clientId);
        }

        if ($employmentId) {
            $query->whereExists(function ($sub) use ($employmentId) {
                $sub->from('client_invoice_lines as cilx')
                    ->whereColumn('cilx.client_invoice_id', 'ci.id')
                    ->where('cilx.employment_id', $employmentId);
            });
        }

        $rows = $query
            ->selectRaw("UPPER(COALESCE(NULLIF(cip.currency, ''), NULLIF(ci.display_currency, ''), NULLIF(ci.foreign_currency, ''), 'USD')) as currency")
            ->selectRaw('SUM(COALESCE(cip.amount, 0)) as total_amount')
            ->groupByRaw("UPPER(COALESCE(NULLIF(cip.currency, ''), NULLIF(ci.display_currency, ''), NULLIF(ci.foreign_currency, ''), 'USD'))")
            ->get();

        $bucket = [];

        foreach ($rows as $row) {
            $currency = strtoupper((string) $row->currency);

            if (blank($currency)) {
                continue;
            }

            $bucket[$currency] = round((float) ($bucket[$currency] ?? 0) + (float) $row->total_amount, 2);
        }

        return $this->normalizeCurrencyMap($bucket);
    }

    protected function salaryAmountsByCurrency($query): array
    {
        $bucket = $query
            ->clone()
            ->selectRaw("UPPER(COALESCE(NULLIF(ss.currency, ''), 'USD')) as currency")
            ->selectRaw('SUM(COALESCE(ss.net_amount, 0)) as total')
            ->groupByRaw("UPPER(COALESCE(NULLIF(ss.currency, ''), 'USD'))")
            ->pluck('total', 'currency')
            ->toArray();

        return $this->normalizeCurrencyMap($bucket);
    }

protected function expenseAmountsByCurrency($query): array
    {
        $rows = $query
            ->selectRaw('UPPER(fe.currency) as currency, SUM(fe.amount) as total_amount')
            ->whereNotNull('fe.currency')
            ->groupByRaw('UPPER(fe.currency)')
            ->get();

        $bucket = [];

        foreach ($rows as $row) {
            $currency = strtoupper((string) $row->currency);

            if (blank($currency)) {
                continue;
            }

            $bucket[$currency] = round((float) $row->total_amount, 2);
        }

        return $this->normalizeCurrencyMap($bucket);
    }

    protected function projectRevenueBuckets(
        Carbon $startDate,
        Carbon $endDate,
        ?int $projectId,
        ?int $clientId,
        ?int $employmentId
    ): array {
        $bucket = [];

        $rows = $this->invoicePaymentsBaseQuery($startDate, $endDate, $projectId, $clientId, $employmentId)
            ->join('projects as p', 'p.id', '=', 'ci.project_id')
            ->selectRaw('ci.project_id, p.name as project_name')
            ->selectRaw("UPPER(COALESCE(NULLIF(cip.currency, ''), ci.display_currency, ci.foreign_currency, 'USD')) as currency")
            ->selectRaw('SUM(COALESCE(cip.amount, 0)) as total_amount')
            ->groupBy('ci.project_id', 'p.name')
            ->groupByRaw("UPPER(COALESCE(NULLIF(cip.currency, ''), ci.display_currency, ci.foreign_currency, 'USD'))")
            ->get();

        foreach ($rows as $row) {
            $this->addEntityCurrencyAmount(
                $bucket,
                (int) $row->project_id,
                'project_name',
                (string) $row->project_name,
                (string) $row->currency,
                (float) $row->total_amount
            );
        }

        return $bucket;
    }

    protected function projectSalaryBuckets(
        Carbon $startDate,
        Carbon $endDate,
        ?int $projectId,
        ?int $clientId,
        ?int $employmentId
    ): array {
        $bucket = [];

        $rows = $this->salarySlipsStatusBaseQuery($startDate, $endDate, $projectId, $clientId, $employmentId, ['sent_to_bank', 'paid'])
            ->join('projects as p', 'p.id', '=', 'ss.project_id')
            ->selectRaw('ss.project_id, p.name as project_name, UPPER(ss.currency) as currency, SUM(ss.net_amount) as total_amount')
            ->whereNotNull('ss.currency')
            ->groupBy('ss.project_id', 'p.name')
            ->groupByRaw('UPPER(ss.currency)')
            ->get();

        foreach ($rows as $row) {
            $this->addEntityCurrencyAmount($bucket, (int) $row->project_id, 'project_name', (string) $row->project_name, (string) $row->currency, (float) $row->total_amount, 'salary_cost_by_currency');
        }

        return $bucket;
    }

    protected function projectExpenseBuckets(
        Carbon $startDate,
        Carbon $endDate,
        ?int $projectId,
        ?int $clientId,
        ?int $employmentId
    ): array {
        $bucket = [];

        if (! Schema::hasColumn('finance_expenses', 'project_id')) {
            return $bucket;
        }

        $rows = $this->expensesBaseQuery($startDate, $endDate, $projectId, $clientId, $employmentId)
            ->join('projects as p', 'p.id', '=', 'fe.project_id')
            ->selectRaw('fe.project_id, p.name as project_name, UPPER(fe.currency) as currency, SUM(fe.amount) as total_amount')
            ->whereNotNull('fe.currency')
            ->groupBy('fe.project_id', 'p.name')
            ->groupByRaw('UPPER(fe.currency)')
            ->get();

        foreach ($rows as $row) {
            $this->addEntityCurrencyAmount($bucket, (int) $row->project_id, 'project_name', (string) $row->project_name, (string) $row->currency, (float) $row->total_amount, 'expenses_by_currency');
        }

        return $bucket;
    }

    protected function clientRevenueBuckets(
        Carbon $startDate,
        Carbon $endDate,
        ?int $projectId,
        ?int $clientId,
        ?int $employmentId
    ): array {
        $bucket = [];

        $rows = $this->invoicePaymentsBaseQuery($startDate, $endDate, $projectId, $clientId, $employmentId)
            ->join('clients as c', 'c.id', '=', 'ci.client_id')
            ->selectRaw('ci.client_id as client_id, c.name as client_name')
            ->selectRaw("UPPER(COALESCE(NULLIF(cip.currency, ''), ci.display_currency, ci.foreign_currency, 'USD')) as currency")
            ->selectRaw('SUM(COALESCE(cip.amount, 0)) as total_amount')
            ->groupBy('ci.client_id', 'c.name')
            ->groupByRaw("UPPER(COALESCE(NULLIF(cip.currency, ''), ci.display_currency, ci.foreign_currency, 'USD'))")
            ->get();

        foreach ($rows as $row) {
            $this->addEntityCurrencyAmount(
                $bucket,
                (int) $row->client_id,
                'client_name',
                (string) $row->client_name,
                (string) $row->currency,
                (float) $row->total_amount
            );
        }

        return $bucket;
    }

    protected function clientSalaryBuckets(
        Carbon $startDate,
        Carbon $endDate,
        ?int $projectId,
        ?int $clientId,
        ?int $employmentId
    ): array {
        $bucket = [];

        $rows = $this->salarySlipsStatusBaseQuery($startDate, $endDate, $projectId, $clientId, $employmentId, ['sent_to_bank', 'paid'])
            ->join('clients as c', 'c.id', '=', 'ss.client_id')
            ->selectRaw('ss.client_id as client_id, c.name as client_name, UPPER(ss.currency) as currency, SUM(ss.net_amount) as total_amount')
            ->whereNotNull('ss.currency')
            ->groupBy('ss.client_id', 'c.name')
            ->groupByRaw('UPPER(ss.currency)')
            ->get();

        foreach ($rows as $row) {
            $this->addEntityCurrencyAmount($bucket, (int) $row->client_id, 'client_name', (string) $row->client_name, (string) $row->currency, (float) $row->total_amount, 'salary_cost_by_currency');
        }

        return $bucket;
    }

    protected function clientExpenseBuckets(
        Carbon $startDate,
        Carbon $endDate,
        ?int $projectId,
        ?int $clientId,
        ?int $employmentId
    ): array {
        $bucket = [];

        if (! Schema::hasColumn('finance_expenses', 'client_id')) {
            return $bucket;
        }

        $rows = $this->expensesBaseQuery($startDate, $endDate, $projectId, $clientId, $employmentId)
            ->join('clients as c', 'c.id', '=', 'fe.client_id')
            ->selectRaw('fe.client_id as client_id, c.name as client_name, UPPER(fe.currency) as currency, SUM(fe.amount) as total_amount')
            ->whereNotNull('fe.currency')
            ->groupBy('fe.client_id', 'c.name')
            ->groupByRaw('UPPER(fe.currency)')
            ->get();

        foreach ($rows as $row) {
            $this->addEntityCurrencyAmount($bucket, (int) $row->client_id, 'client_name', (string) $row->client_name, (string) $row->currency, (float) $row->total_amount, 'expenses_by_currency');
        }

        return $bucket;
    }

    protected function employeeRevenueBuckets(
        Carbon $startDate,
        Carbon $endDate,
        ?int $projectId,
        ?int $clientId,
        ?int $employmentId
    ): array {
        /*
         * Employee revenue is allocated from actual cleared receipts only.
         * The receipt is distributed by each invoice line share so employee totals
         * do not count unpaid invoice value.
         */
        $bucket = [];

        $query = $this->invoicePaymentsBaseQuery($startDate, $endDate, $projectId, $clientId, $employmentId)
            ->join('client_invoice_lines as cil', 'cil.client_invoice_id', '=', 'ci.id')
            ->join('employments as e', 'e.id', '=', 'cil.employment_id')
            ->whereNotNull('cil.employment_id')
            ->selectRaw('cil.employment_id, e.employee_name, e.position_title')
            ->selectRaw("
                CASE
                    WHEN cip.applies_to = 'local' THEN UPPER(COALESCE(NULLIF(ci.local_currency, ''), NULLIF(cip.currency, ''), 'LYD'))
                    ELSE UPPER(COALESCE(NULLIF(ci.foreign_currency, ''), NULLIF(ci.display_currency, ''), NULLIF(cip.currency, ''), 'USD'))
                END as currency
            ")
            ->selectRaw("
                SUM(
                    CASE
                        WHEN cip.applies_to = 'local'
                            THEN COALESCE(cip.amount_in_invoice_currency, cip.amount, 0)
                                 * (
                                    COALESCE(cil.local_amount, 0)
                                    / NULLIF(COALESCE(ci.local_amount_due, 0), 0)
                                 )
                        ELSE COALESCE(cip.amount_in_invoice_currency, cip.amount, 0)
                             * (
                                COALESCE(cil.foreign_amount, 0)
                                / NULLIF(COALESCE(ci.foreign_amount_due, 0), 0)
                             )
                    END
                ) as total_amount
            ")
            ->groupBy('cil.employment_id', 'e.employee_name', 'e.position_title')
            ->groupByRaw("
                CASE
                    WHEN cip.applies_to = 'local' THEN UPPER(COALESCE(NULLIF(ci.local_currency, ''), NULLIF(cip.currency, ''), 'LYD'))
                    ELSE UPPER(COALESCE(NULLIF(ci.foreign_currency, ''), NULLIF(ci.display_currency, ''), NULLIF(cip.currency, ''), 'USD'))
                END
            ");

        $rows = $query->get();

        foreach ($rows as $row) {
            $this->addEntityCurrencyAmount(
                $bucket,
                (int) $row->employment_id,
                'employee_name',
                (string) $row->employee_name,
                (string) $row->currency,
                (float) $row->total_amount
            );

            $bucket[(int) $row->employment_id]['position_title'] = (string) ($row->position_title ?: '-');
        }

        return $bucket;
    }

    protected function employeeSalaryBuckets(
        Carbon $startDate,
        Carbon $endDate,
        ?int $projectId,
        ?int $clientId,
        ?int $employmentId
    ): array {
        $bucket = [];

        $rows = $this->salarySlipsStatusBaseQuery($startDate, $endDate, $projectId, $clientId, $employmentId, ['sent_to_bank', 'paid'])
            ->join('employments as e', 'e.id', '=', 'ss.employment_id')
            ->selectRaw('ss.employment_id, e.employee_name, e.position_title, UPPER(ss.currency) as currency, SUM(ss.net_amount) as total_amount')
            ->whereNotNull('ss.currency')
            ->groupBy('ss.employment_id', 'e.employee_name', 'e.position_title')
            ->groupByRaw('UPPER(ss.currency)')
            ->get();

        foreach ($rows as $row) {
            $this->addEntityCurrencyAmount($bucket, (int) $row->employment_id, 'employee_name', (string) $row->employee_name, (string) $row->currency, (float) $row->total_amount, 'salary_cost_by_currency');
            $bucket[(int) $row->employment_id]['position_title'] = (string) ($row->position_title ?: '-');
        }

        return $bucket;
    }

    protected function employeeExpenseBuckets(
        Carbon $startDate,
        Carbon $endDate,
        ?int $projectId,
        ?int $clientId,
        ?int $employmentId
    ): array {
        $bucket = [];

        if (! Schema::hasColumn('finance_expenses', 'employment_id')) {
            return $bucket;
        }

        $rows = $this->expensesBaseQuery($startDate, $endDate, $projectId, $clientId, $employmentId)
            ->join('employments as e', 'e.id', '=', 'fe.employment_id')
            ->selectRaw('fe.employment_id, e.employee_name, e.position_title, UPPER(fe.currency) as currency, SUM(fe.amount) as total_amount')
            ->whereNotNull('fe.currency')
            ->groupBy('fe.employment_id', 'e.employee_name', 'e.position_title')
            ->groupByRaw('UPPER(fe.currency)')
            ->get();

        foreach ($rows as $row) {
            $this->addEntityCurrencyAmount($bucket, (int) $row->employment_id, 'employee_name', (string) $row->employee_name, (string) $row->currency, (float) $row->total_amount, 'expenses_by_currency');
            $bucket[(int) $row->employment_id]['position_title'] = (string) ($row->position_title ?: '-');
        }

        return $bucket;
    }

    protected function mergeEntityBuckets(
        array $revenue,
        array $salary,
        array $expenses,
        string $idKey,
        string $labelKey
    ): array {
        $ids = collect(array_merge(array_keys($revenue), array_keys($salary), array_keys($expenses)))
            ->unique()
            ->values();

        return $ids
            ->map(function ($id) use ($revenue, $salary, $expenses, $idKey, $labelKey) {
                $label = $revenue[$id][$labelKey]
                    ?? ($salary[$id][$labelKey] ?? ($expenses[$id][$labelKey] ?? ($labelKey . ' #' . $id)));

                $revenueMap = $this->normalizeCurrencyMap($revenue[$id]['revenue_by_currency'] ?? []);
                $salaryMap = $this->normalizeCurrencyMap($salary[$id]['salary_cost_by_currency'] ?? []);
                $expensesMap = $this->normalizeCurrencyMap($expenses[$id]['expenses_by_currency'] ?? []);
                $netMap = $this->subtractCurrencyMaps(
                    $this->subtractCurrencyMaps($revenueMap, $salaryMap),
                    $expensesMap
                );

                return [
                    $idKey => $id,
                    $labelKey => $label,
                    'revenue_by_currency' => $revenueMap,
                    'salary_cost_by_currency' => $salaryMap,
                    'expenses_by_currency' => $expensesMap,
                    'net_by_currency' => $this->normalizeCurrencyMap($netMap),
                ];
            })
            ->sortBy($labelKey)
            ->values()
            ->all();
    }

    protected function addEntityCurrencyAmount(
        array &$bucket,
        int $id,
        string $labelKey,
        string $labelValue,
        string $currency,
        float $amount,
        string $mapKey = 'revenue_by_currency'
    ): void {
        $currency = strtoupper(trim($currency));

        if ($id <= 0 || blank($currency)) {
            return;
        }

        if (! isset($bucket[$id])) {
            $bucket[$id] = [
                $labelKey => $labelValue ?: '-',
                'revenue_by_currency' => [],
                'salary_cost_by_currency' => [],
                'expenses_by_currency' => [],
            ];
        }

        $bucket[$id][$labelKey] = $labelValue ?: ($bucket[$id][$labelKey] ?? '-');
        $bucket[$id][$mapKey][$currency] = round((float) ($bucket[$id][$mapKey][$currency] ?? 0) + $amount, 2);
    }

    protected function subtractCurrencyMaps(array $base, array $subtract, bool $floorAtZero = false): array
    {
        $currencies = $this->collectAllCurrencies($base, $subtract);
        $result = [];

        foreach ($currencies as $currency) {
            $value = (float) ($base[$currency] ?? 0) - (float) ($subtract[$currency] ?? 0);

            if ($floorAtZero) {
                $value = max(0, $value);
            }

            $result[$currency] = round($value, 2);
        }

        return $this->normalizeCurrencyMap($result);
    }

    protected function collectAllCurrencies(array ...$maps): array
    {
        return collect($maps)
            ->flatMap(fn (array $map) => array_keys($map))
            ->filter(fn ($currency) => filled($currency))
            ->map(fn ($currency) => strtoupper((string) $currency))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    protected function normalizeCurrencyMap(array $map): array
    {
        return collect($map)
            ->filter(fn ($amount, $currency) => filled($currency))
            ->mapWithKeys(fn ($amount, $currency) => [
                strtoupper((string) $currency) => round((float) $amount, 2),
            ])
            ->sortKeys()
            ->toArray();
    }

    protected function invoiceLinesBaseQuery(Carbon $startDate, Carbon $endDate, ?int $projectId, ?int $clientId, ?int $employmentId)
    {
        $query = DB::table('client_invoice_lines as cil')
            ->join('client_invoices as ci', 'ci.id', '=', 'cil.client_invoice_id')
            ->whereDate('ci.invoice_date', '>=', $startDate->toDateString())
            ->whereDate('ci.invoice_date', '<=', $endDate->toDateString());

        if ($projectId) {
            $query->where('cil.project_id', $projectId);
        }

        if ($clientId) {
            $query->where('ci.client_id', $clientId);
        }

        if ($employmentId) {
            $query->where('cil.employment_id', $employmentId);
        }

        return $query;
    }

    protected function salarySlipsBaseQuery(Carbon $startDate, Carbon $endDate, ?int $projectId, ?int $clientId, ?int $employmentId)
    {
        $query = DB::table('salary_slips as ss')
            ->whereIn('ss.status', ['sent_to_bank', 'paid'])
            ->whereDate('ss.period_start', '<=', $endDate->toDateString())
            ->whereDate('ss.period_end', '>=', $startDate->toDateString());

        if ($projectId) {
            $query->where('ss.project_id', $projectId);
        }

        if ($clientId) {
            $query->where('ss.client_id', $clientId);
        }

        if ($employmentId) {
            $query->where('ss.employment_id', $employmentId);
        }

        return $query;
    }


    protected function salarySlipsStatusBaseQuery(Carbon $startDate, Carbon $endDate, ?int $projectId, ?int $clientId, ?int $employmentId, array $statuses)
    {
        $query = DB::table('salary_slips as ss')
            ->whereIn('ss.status', $statuses)
            ->whereDate('ss.period_start', '<=', $endDate->toDateString())
            ->whereDate('ss.period_end', '>=', $startDate->toDateString());

        if ($projectId) {
            $query->where('ss.project_id', $projectId);
        }

        if ($clientId) {
            $query->where('ss.client_id', $clientId);
        }

        if ($employmentId) {
            $query->where('ss.employment_id', $employmentId);
        }

        return $query;
    }

    protected function expensesBaseQuery(Carbon $startDate, Carbon $endDate, ?int $projectId, ?int $clientId, ?int $employmentId)
    {
        $query = DB::table('finance_expenses as fe')
            ->whereNotIn('fe.status', ['cancelled'])
            ->whereDate('fe.expense_date', '>=', $startDate->toDateString())
            ->whereDate('fe.expense_date', '<=', $endDate->toDateString());

        if ($projectId && Schema::hasColumn('finance_expenses', 'project_id')) {
            $query->where('fe.project_id', $projectId);
        }

        if ($clientId && Schema::hasColumn('finance_expenses', 'client_id')) {
            $query->where('fe.client_id', $clientId);
        }

        if ($employmentId && Schema::hasColumn('finance_expenses', 'employment_id')) {
            $query->where('fe.employment_id', $employmentId);
        }

        $query->where('fe.status', \App\Models\FinanceExpense::STATUS_PAID);

        // Critical rule for Global Finance Totals:
        // Count expenses only when they are actually paid through treasury.
        // If an expense was moved back to Approved/Draft, treasury_transaction_id
        // must be null and it must not be counted.
        if (Schema::hasColumn('finance_expenses', 'treasury_transaction_id')) {
            $query->whereNotNull('fe.treasury_transaction_id');
        }

        return $query;
    }

    protected function resolveDateRange(array $filters): array
    {
        if (! empty($filters['date_from']) || ! empty($filters['date_to'])) {
            $start = ! empty($filters['date_from'])
                ? Carbon::parse($filters['date_from'])->startOfDay()
                : $this->resolveSystemStartDate();

            $end = ! empty($filters['date_to'])
                ? Carbon::parse($filters['date_to'])->endOfDay()
                : Carbon::now()->endOfDay();

            return [$start, $end];
        }

        if (! empty($filters['year']) && ! empty($filters['month'])) {
            $year = (int) $filters['year'];
            $month = (int) $filters['month'];

            $start = Carbon::create($year, $month, 1)->startOfMonth()->startOfDay();
            $end = $start->copy()->endOfMonth()->endOfDay();

            return [$start, $end];
        }

        if (! empty($filters['year'])) {
            $year = (int) $filters['year'];

            $start = Carbon::create($year, 1, 1)->startOfYear()->startOfDay();
            $end = Carbon::create($year, 12, 31)->endOfYear()->endOfDay();

            return [$start, $end];
        }

        return [
            $this->resolveSystemStartDate(),
            Carbon::now()->endOfDay(),
        ];
    }

    protected function resolveSystemStartDate(): Carbon
    {
        $dates = collect();

        if (Schema::hasTable('client_invoices') && Schema::hasColumn('client_invoices', 'invoice_date')) {
            $dates->push(DB::table('client_invoices')->whereNotNull('invoice_date')->min('invoice_date'));
        }

        if (Schema::hasTable('salary_slips') && Schema::hasColumn('salary_slips', 'period_start')) {
            $dates->push(DB::table('salary_slips')->whereNotNull('period_start')->min('period_start'));
        }

        if (Schema::hasTable('finance_expenses') && Schema::hasColumn('finance_expenses', 'expense_date')) {
            $dates->push(DB::table('finance_expenses')->whereNotNull('expense_date')->min('expense_date'));
        }

        if (Schema::hasTable('treasury_transactions') && Schema::hasColumn('treasury_transactions', 'transaction_date')) {
            $dates->push(DB::table('treasury_transactions')->whereNotNull('transaction_date')->min('transaction_date'));
        }

        $firstDate = $dates
            ->filter()
            ->sort()
            ->first();

        return $firstDate
            ? Carbon::parse($firstDate)->startOfDay()
            : Carbon::now()->startOfYear()->startOfDay();
    }

}