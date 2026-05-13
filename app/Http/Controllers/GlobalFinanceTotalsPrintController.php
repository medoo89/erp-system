<?php

namespace App\Http\Controllers;

use App\Services\FinanceTotalsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GlobalFinanceTotalsPrintController extends Controller
{
    public function show(Request $request): View
    {
        $filters = [
            'year' => $request->integer('year') ?: null,
            'month' => $request->integer('month') ?: null,
            'date_from' => $request->input('date_from') ?: null,
            'date_to' => $request->input('date_to') ?: null,
            'client_id' => $request->integer('client_id') ?: null,
            'project_id' => $request->integer('project_id') ?: null,
            'employment_id' => $request->integer('employment_id') ?: null,
        ];

        $service = app(FinanceTotalsService::class);

        $totals = $service->build($filters);

        $baseCurrency = strtoupper((string) ($request->input('base_currency') ?: 'EUR'));

        $exchangeRates = [
            'USD' => (float) ($request->input('rate_usd') ?: 0),
            'EUR' => (float) ($request->input('rate_eur') ?: 0),
            'LYD' => (float) ($request->input('rate_lyd') ?: 0),
            'GBP' => (float) ($request->input('rate_gbp') ?: 0),
        ];

        if (($exchangeRates[$baseCurrency] ?? 0) <= 0) {
            $exchangeRates[$baseCurrency] = 1.0;
        }

        $summary = $totals['summary'] ?? [];

        $convertedSummary = [
            'base_currency' => $baseCurrency,
            'revenue_total' => $this->convertCurrencyMapToBase(
                $summary['revenue_by_currency'] ?? [],
                $baseCurrency,
                $exchangeRates
            ),
            'salary_cost_total' => $this->convertCurrencyMapToBase(
                $summary['salary_cost_by_currency'] ?? [],
                $baseCurrency,
                $exchangeRates
            ),
            'salary_paid_total' => $this->convertCurrencyMapToBase(
                $summary['salary_paid_by_currency'] ?? [],
                $baseCurrency,
                $exchangeRates
            ),
            'salary_remaining_total' => $this->convertCurrencyMapToBase(
                $summary['salary_remaining_by_currency'] ?? [],
                $baseCurrency,
                $exchangeRates
            ),
            'expenses_total' => $this->convertCurrencyMapToBase(
                $summary['expenses_by_currency'] ?? [],
                $baseCurrency,
                $exchangeRates
            ),
            'net_total' => $this->convertCurrencyMapToBase(
                $summary['net_by_currency'] ?? [],
                $baseCurrency,
                $exchangeRates
            ),
        ];

        $totals['by_client'] = $this->appendConvertedTotalsToRows(
            $totals['by_client'] ?? [],
            $baseCurrency,
            $exchangeRates
        );

        $totals['by_project'] = $this->appendConvertedTotalsToRows(
            $totals['by_project'] ?? [],
            $baseCurrency,
            $exchangeRates
        );

        $totals['by_employee'] = $this->appendConvertedTotalsToRows(
            $totals['by_employee'] ?? [],
            $baseCurrency,
            $exchangeRates
        );

        return view('print.global-finance-totals', [
            'totals' => $totals,
            'filters' => $filters,
            'baseCurrency' => $baseCurrency,
            'exchangeRates' => $exchangeRates,
            'convertedSummary' => $convertedSummary,
            'generatedAt' => now(),
        ]);
    }

    protected function appendConvertedTotalsToRows(array $rows, string $baseCurrency, array $exchangeRates): array
    {
        return collect($rows)
            ->map(function (array $row) use ($baseCurrency, $exchangeRates) {
                $row['converted_revenue_total'] = $this->convertCurrencyMapToBase(
                    $row['revenue_by_currency'] ?? [],
                    $baseCurrency,
                    $exchangeRates
                );

                $row['converted_salary_cost_total'] = $this->convertCurrencyMapToBase(
                    $row['salary_cost_by_currency'] ?? [],
                    $baseCurrency,
                    $exchangeRates
                );

                $row['converted_expenses_total'] = $this->convertCurrencyMapToBase(
                    $row['expenses_by_currency'] ?? [],
                    $baseCurrency,
                    $exchangeRates
                );

                $row['converted_net_total'] = $this->convertCurrencyMapToBase(
                    $row['net_by_currency'] ?? [],
                    $baseCurrency,
                    $exchangeRates
                );

                return $row;
            })
            ->values()
            ->all();
    }

    protected function convertCurrencyMapToBase(array $currencyMap, string $baseCurrency, array $exchangeRates): float
    {
        $total = 0.0;

        foreach ($currencyMap as $currency => $amount) {
            $currency = strtoupper((string) $currency);
            $amount = (float) $amount;

            if ($currency === $baseCurrency) {
                $total += $amount;
                continue;
            }

            $rate = (float) ($exchangeRates[$currency] ?? 0);

            if ($rate <= 0) {
                continue;
            }

            $total += $amount * $rate;
        }

        return round($total, 2);
    }

    public function print(Request $request)
    {
        if (method_exists($this, 'show')) {
            return $this->show($request);
        }

        if (method_exists($this, '__invoke')) {
            return $this->__invoke($request);
        }

        return view('print.global-finance-totals', [
            'filters' => $request->query(),
        ]);
    }

}