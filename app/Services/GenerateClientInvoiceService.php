<?php

namespace App\Services;

use App\Models\ClientContractTerm;
use App\Models\ClientInvoice;
use App\Models\ClientInvoiceLine;
use App\Models\InvoiceProfile;
use App\Models\Project;
use App\Models\SalarySlip;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GenerateClientInvoiceService
{
    public function createDraftForProjectMonth(
        Project $project,
        array $employmentIds,
        int $year,
        int $month,
        ?string $billingCurrency = null,
        ?float $exchangeRate = null,
        ?float $foreignPercentage = null,
        ?float $localPercentage = null,
        ?string $localCurrency = null,
        ?string $invoiceDate = null,
        ?int $invoiceProfileId = null
    ): ClientInvoice {
        $project->loadMissing('client', 'contractTerms', 'defaultInvoiceProfile');

        $contractTerm = $this->resolveContractTerm($project);

        $invoiceProfile = $project->defaultInvoiceProfile;

        if (! $invoiceProfile) {
            $invoiceProfile = $this->resolveInvoiceProfile(
                $invoiceProfileId,
                $billingCurrency ?: $contractTerm?->currency
            );
        }

        $defaultBillingCurrency = strtoupper((string) ($billingCurrency ?: ($contractTerm?->currency ?: ($invoiceProfile?->currency ?: 'EUR'))));
        $resolvedExchangeRate = $exchangeRate ?? ($contractTerm?->default_exchange_rate ? (float) $contractTerm->default_exchange_rate : null);
        $resolvedForeignPercentage = $foreignPercentage ?? (float) ($contractTerm?->foreign_percentage ?? 100);
        $resolvedLocalPercentage = $localPercentage ?? (float) ($contractTerm?->local_percentage ?? 0);
        $resolvedLocalCurrency = strtoupper((string) ($localCurrency ?: ($contractTerm?->local_currency ?: 'LYD')));

        $monthStart = Carbon::create($year, $month, 1)->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth()->startOfDay();
        $invoiceDateValue = $invoiceDate ? Carbon::parse($invoiceDate)->toDateString() : now()->toDateString();

        $salarySlips = SalarySlip::query()
            ->with(['employment.currentFinanceProfile', 'days'])
            ->where('project_id', $project->id)
            ->where('salary_year', $year)
            ->where('salary_month', $month)
            ->whereIn('status', [
                SalarySlip::STATUS_APPROVED,
                SalarySlip::STATUS_LOCKED,
            ])
            ->when(! empty($employmentIds), fn ($query) => $query->whereIn('employment_id', $employmentIds))
            ->orderBy('employment_id')
            ->get();

        if ($salarySlips->isEmpty()) {
            throw new \RuntimeException('No approved or locked salary slips were found for the selected month and employees.');
        }

        $invalidEmployees = [];

        foreach ($salarySlips as $salarySlip) {
            $employment = $salarySlip->employment;
            $profile = $employment?->currentFinanceProfile;

            $billingRateValue = (float) ($profile?->client_billing_rate ?? 0);
            $billingCurrencyValue = strtoupper((string) ($profile?->client_billing_currency ?? ''));

            if (! $employment || ! $profile || $billingRateValue <= 0 || blank($billingCurrencyValue)) {
                $invalidEmployees[] = $employment?->employee_name ?: ('Employment #' . ($salarySlip->employment_id ?? 'Unknown'));
            }
        }

        if (! empty($invalidEmployees)) {
            throw new \RuntimeException(
                'The following employees are not invoice-ready because client billing data is missing or incomplete: '
                . implode(', ', array_unique($invalidEmployees))
                . '.'
            );
        }

        return DB::transaction(function () use (
            $project,
            $contractTerm,
            $invoiceProfile,
            $defaultBillingCurrency,
            $resolvedExchangeRate,
            $resolvedForeignPercentage,
            $resolvedLocalPercentage,
            $resolvedLocalCurrency,
            $monthStart,
            $monthEnd,
            $invoiceDateValue,
            $salarySlips
        ) {
            $invoice = ClientInvoice::create([
                'invoice_number' => $this->generateInvoiceNumber($project),
                'client_id' => $project->client_id,
                'project_id' => $project->id,
                'invoice_profile_id' => $invoiceProfile?->id,
                'created_by' => Auth::id(),
                'invoice_date' => $invoiceDateValue,
                'period_start' => $monthStart->toDateString(),
                'period_end' => $monthEnd->toDateString(),
                'status' => ClientInvoice::STATUS_DRAFT,
                'payment_terms_label' => $this->buildPaymentTermsLabel(
                    $resolvedForeignPercentage,
                    $defaultBillingCurrency,
                    $resolvedLocalPercentage,
                    $resolvedLocalCurrency
                ),
                'foreign_currency' => $defaultBillingCurrency,
                'foreign_percentage' => $resolvedForeignPercentage,
                'local_currency' => $resolvedLocalCurrency,
                'local_percentage' => $resolvedLocalPercentage,
                'exchange_rate' => $resolvedExchangeRate,
                'display_currency' => $defaultBillingCurrency,
                'bill_to_name' => $project->client?->name,
                'bill_to_address' => $project->client?->address,
                'bill_to_phone' => $project->client?->phone,
                'bank_name' => $invoiceProfile?->bank_name,
                'swift_code' => $invoiceProfile?->swift_code,
                'account_number_lyd' => $invoiceProfile?->account_number_lyd,
                'iban_lyd' => $invoiceProfile?->iban_lyd,
                'iban_usd' => $invoiceProfile?->iban_usd,
                'iban_eur' => $invoiceProfile?->iban_eur,
                'notes' => 'Draft invoice generated from approved / locked salary slips and employee billing profiles.',
                'terms_text' => $invoiceProfile?->terms_text,
            ]);

            $subtotal = 0.0;
            $sortOrder = 1;

            foreach ($salarySlips as $salarySlip) {
                $employment = $salarySlip->employment;

                if (! $employment) {
                    continue;
                }

                $paidDays = (float) $salarySlip->paidDaysCount();

                if ($paidDays <= 0) {
                    continue;
                }

                $profile = $employment->currentFinanceProfile;

                $lineBillingRate = (float) ($profile?->client_billing_rate ?? 0);
                $lineBillingCurrency = strtoupper((string) ($profile?->client_billing_currency ?? ''));

                if ($lineBillingRate <= 0 || blank($lineBillingCurrency)) {
                    throw new \RuntimeException(
                        'Client billing configuration is missing for employee: ' . ($employment->employee_name ?: ('#' . $employment->id))
                    );
                }

                $amount = round($paidDays * $lineBillingRate, 2);

                $foreignAmount = round($amount * ($resolvedForeignPercentage / 100), 2);
                $localAmountForeignEquivalent = round($amount * ($resolvedLocalPercentage / 100), 2);
                $localAmount = $resolvedExchangeRate
                    ? round($localAmountForeignEquivalent * $resolvedExchangeRate, 2)
                    : 0;

                $scope = trim(implode("\n", array_filter([
                    'Candidate Name: ' . ($employment->employee_name ?: '-'),
                    'Position: ' . ($employment->position_title ?: '-'),
                    'Project Name: ' . ($project->name ?: '-'),
                    'Date & Duration: ' . (optional($salarySlip->period_start)->format('d M Y') ?: '-') . ' - ' . (optional($salarySlip->period_end)->format('d M Y') ?: '-'),
                    'Paid Duration: ' . rtrim(rtrim(number_format($paidDays, 2), '0'), '.') . ' day(s)',
                    'Billing Rate: ' . number_format($lineBillingRate, 2) . ' ' . $lineBillingCurrency,
                ])));

                ClientInvoiceLine::create([
                    'client_invoice_id' => $invoice->id,
                    'employment_id' => $employment->id,
                    'project_id' => $project->id,
                    'salary_slip_id' => $salarySlip->id,
                    'client_contract_term_id' => $contractTerm?->id,
                    'service_title' => 'Manpower Supply / Daily Rate',
                    'position_title' => $employment->position_title,
                    'candidate_name' => $employment->employee_name,
                    'project_name' => $project->name,
                    'service_period_start' => $salarySlip->period_start?->toDateString(),
                    'service_period_end' => $salarySlip->period_end?->toDateString(),
                    'service_month_label' => Carbon::create($salarySlip->salary_year, $salarySlip->salary_month, 1)->format('F Y'),
                    'quantity' => $paidDays,
                    'unit_rate' => $lineBillingRate,
                    'amount' => $amount,
                    'currency' => $lineBillingCurrency,
                    'foreign_amount' => $foreignAmount,
                    'local_amount_foreign_equivalent' => $localAmountForeignEquivalent,
                    'local_amount' => $localAmount,
                    'foreign_currency' => $lineBillingCurrency,
                    'local_currency' => $resolvedLocalCurrency,
                    'scope_description' => $scope,
                    'line_notes' => null,
                    'sort_order' => $sortOrder++,
                ]);

                $subtotal += $amount;
            }

            if ($subtotal <= 0) {
                throw new \RuntimeException('No payable approved or locked salary slips were found.');
            }

            $subtotal = round($subtotal, 2);
            $taxPercent = 0;
            $taxAmount = 0;
            $total = $subtotal;

            $foreignAmountDue = round($total * ($resolvedForeignPercentage / 100), 2);
            $localAmountForeignEquivalent = round($total * ($resolvedLocalPercentage / 100), 2);
            $localAmountDue = $resolvedExchangeRate
                ? round($localAmountForeignEquivalent * $resolvedExchangeRate, 2)
                : 0;

            $invoice->update([
                'subtotal_amount' => $subtotal,
                'tax_percent' => $taxPercent,
                'tax_amount' => $taxAmount,
                'total_amount' => $total,
                'foreign_amount_due' => $foreignAmountDue,
                'local_amount_due' => $localAmountDue,
                'local_amount_foreign_equivalent' => $localAmountForeignEquivalent,
                'terms_text' => filled($invoice->terms_text)
                    ? $invoice->terms_text
                    : $this->buildTermsText(
                        $resolvedForeignPercentage,
                        $defaultBillingCurrency,
                        $foreignAmountDue,
                        $resolvedLocalPercentage,
                        $resolvedLocalCurrency,
                        $localAmountForeignEquivalent,
                        $resolvedExchangeRate,
                        $localAmountDue
                    ),
            ]);

            return $invoice->fresh('lines', 'client', 'project');
        });
    }

    protected function resolveContractTerm(Project $project): ?ClientContractTerm
    {
        return $project->contractTerms()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderByDesc('effective_from')
            ->first();
    }

    protected function resolveInvoiceProfile(?int $invoiceProfileId, ?string $preferredCurrency = null): ?InvoiceProfile
    {
        if ($invoiceProfileId) {
            return InvoiceProfile::query()
                ->where('is_active', true)
                ->find($invoiceProfileId);
        }

        if ($preferredCurrency) {
            $currencyMatch = InvoiceProfile::query()
                ->where('is_active', true)
                ->where('currency', strtoupper((string) $preferredCurrency))
                ->orderByDesc('is_default')
                ->first();

            if ($currencyMatch) {
                return $currencyMatch;
            }
        }

        return InvoiceProfile::query()
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->first();
    }

    protected function generateInvoiceNumber(Project $project): string
    {
        $prefix = 'INV-' . now()->format('Ymd');
        $projectCode = strtoupper((string) ($project->project_code ?: $project->code ?: 'PRJ'));
        $sequence = str_pad((string) (ClientInvoice::query()->count() + 1), 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$projectCode}-{$sequence}";
    }

    protected function buildPaymentTermsLabel(
        float $foreignPercentage,
        string $foreignCurrency,
        float $localPercentage,
        string $localCurrency
    ): string {
        return rtrim(rtrim(number_format($foreignPercentage, 2), '0'), '.') . '% in ' . $foreignCurrency
            . ' + '
            . rtrim(rtrim(number_format($localPercentage, 2), '0'), '.') . '% in ' . $localCurrency;
    }

    protected function buildTermsText(
        float $foreignPercentage,
        string $foreignCurrency,
        float $foreignAmount,
        float $localPercentage,
        string $localCurrency,
        float $localEquivalentInForeign,
        ?float $exchangeRate,
        float $localAmountDue
    ): string {
        $lines = [];

        $lines[] = rtrim(rtrim(number_format($foreignPercentage, 2), '0'), '.') . '% payable in ' . $foreignCurrency
            . ' = ' . number_format($foreignAmount, 2) . ' ' . $foreignCurrency . '.';

        $lines[] = rtrim(rtrim(number_format($localPercentage, 2), '0'), '.') . '% payable in ' . $localCurrency
            . ' (equivalent to ' . number_format($localEquivalentInForeign, 2) . ' ' . $foreignCurrency . ').';

        if ($exchangeRate) {
            $lines[] = 'Exchange Rate Used: 1 ' . $foreignCurrency . ' = ' . rtrim(rtrim(number_format($exchangeRate, 4), '0'), '.') . ' ' . $localCurrency . '.';
            $lines[] = 'Local Amount Due: ' . number_format($localAmountDue, 2) . ' ' . $localCurrency . '.';
        }

        return implode("\n", $lines);
    }
}