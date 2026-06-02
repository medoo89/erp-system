<?php

namespace App\Models;

use Illuminate\Support\Carbon;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Schema;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientInvoice extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_ISSUED = 'issued';
    public const STATUS_SENT_TO_CLIENT = 'sent_to_client';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_PARTIALLY_PAID = 'partially_paid';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'invoice_number',
        'client_id',
        'project_id',
        'invoice_profile_id',
        'bank_profile_id',
        'treasury_operation_id',
        'created_by',
        'invoice_date',
        'period_start',
        'period_end',
        'status',
        'payment_terms_label',
        'foreign_currency',
        'foreign_percentage',
        'local_currency',
        'local_percentage',
        'exchange_rate',
        'subtotal_amount',
        'tax_percent',
        'tax_amount',
        'total_amount',
        'foreign_amount_due',
        'local_amount_due',
        'local_amount_foreign_equivalent',
        'display_currency',
        'bill_to_name',
        'bill_to_address',
        'bill_to_phone',
        'bank_name',
        'swift_code',
        'account_number_lyd',
        'iban_lyd',
        'iban_usd',
        'iban_eur',
        'notes',
        'terms_text',
    
        'project_contract_id',
        'contract_consumption_amount',
        'contract_consumption_currency',
        'contract_consumption_percent',
        'allocated_contract_tax_amount',
        'allocated_contract_tax_currency',
        'show_contract_tax_on_invoice_document',];

    protected $casts = [
        'invoice_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'foreign_percentage' => 'decimal:2',
        'local_percentage' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'subtotal_amount' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'foreign_amount_due' => 'decimal:2',
        'local_amount_due' => 'decimal:2',
        'local_amount_foreign_equivalent' => 'decimal:2',
    
        'contract_consumption_amount' => 'decimal:2',
        'contract_consumption_percent' => 'decimal:4',
        'allocated_contract_tax_amount' => 'decimal:2',
        'show_contract_tax_on_invoice_document' => 'boolean',];

    protected static function booted(): void
    {
        static::saved(function (self $invoice): void {
            $invoice->syncSplitDocuments();
        });

        static::saving(function (self $invoice): void {
            $invoice->applyContractTaxAllocation();
        });

        static::saving(function (ClientInvoice $invoice) {
            if ($invoice->bank_profile_id) {
                $profile = $invoice->bankProfile ?: BankProfile::query()->find($invoice->bank_profile_id);

                if ($profile) {
                    $invoice->applyBankProfileSnapshot($profile);
                }
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function invoiceProfile(): BelongsTo
    {
        return $this->belongsTo(InvoiceProfile::class, 'invoice_profile_id');
    }

    public function bankProfile(): BelongsTo
    {
        return $this->belongsTo(BankProfile::class, 'bank_profile_id');
    }

    public function treasuryOperation(): BelongsTo
    {
        return $this->belongsTo(TreasuryOperation::class, 'treasury_operation_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(ClientInvoiceLine::class, 'client_invoice_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }


    public function workDays(): HasMany
    {
        return $this->hasMany(ClientInvoiceWorkDay::class, 'client_invoice_id')
            ->orderBy('work_date')
            ->orderBy('id');
    }

    public function paidWorkDaysCount(): float
    {
        return (float) $this->workDays()
            ->where('day_status', ClientInvoiceWorkDay::STATUS_PAID)
            ->sum('billable_units');
    }

    public function notPaidWorkDaysCount(): int
    {
        return (int) $this->workDays()
            ->where('day_status', ClientInvoiceWorkDay::STATUS_NOT_PAID)
            ->count();
    }

    public function absentWorkDaysCount(): int
    {
        return (int) $this->workDays()
            ->where('day_status', ClientInvoiceWorkDay::STATUS_ABSENT)
            ->count();
    }


    public function documents(): HasMany
    {
        return $this->hasMany(ClientInvoiceDocument::class, 'client_invoice_id')
            ->orderBy('document_type')
            ->orderBy('id');
    }


    public function generateTimesheetFromSalarySlips(bool $replaceExisting = true): array
    {
        if (! $this->exists) {
            return [
                'salary_slips' => 0,
                'work_days' => 0,
                'message' => 'Invoice must be saved before generating timesheet.',
            ];
        }

        $this->loadMissing(['lines.salarySlip.days', 'lines.salarySlip.employment']);

        $salarySlips = $this->lines
            ->pluck('salarySlip')
            ->filter()
            ->unique('id')
            ->values();

        if ($salarySlips->isEmpty()) {
            return [
                'salary_slips' => 0,
                'work_days' => 0,
                'message' => 'No salary slips linked to invoice lines.',
            ];
        }

        if ($replaceExisting) {
            $this->workDays()->delete();
        }

        $created = 0;

        foreach ($salarySlips as $salarySlip) {
            $line = $this->lines
                ->firstWhere('salary_slip_id', $salarySlip->id);

            $days = $salarySlip->days()
                ->orderBy('work_date')
                ->get();

            foreach ($days as $salaryDay) {
                $isPaid = (bool) ($salaryDay->is_paid_day ?? false);
                $attendanceStatus = (string) ($salaryDay->attendance_status ?? '');
                $dayType = (string) ($salaryDay->day_type ?? '');

                $isAbsent = in_array($attendanceStatus, ['absent', 'unpaid_leave'], true)
                    || in_array($dayType, ['absent', 'unpaid_leave'], true);

                if ($isPaid) {
                    $invoiceStatus = ClientInvoiceWorkDay::STATUS_PAID;
                    $isBillable = true;
                    $units = (float) ($salaryDay->pay_multiplier ?? 1);
                    $units = $units > 0 ? $units : 1;
                } elseif ($isAbsent) {
                    $invoiceStatus = ClientInvoiceWorkDay::STATUS_ABSENT;
                    $isBillable = false;
                    $units = 0;
                } else {
                    $invoiceStatus = ClientInvoiceWorkDay::STATUS_NOT_PAID;
                    $isBillable = false;
                    $units = 0;
                }

                $this->workDays()->create([
                    'client_invoice_line_id' => $line?->id,
                    'employment_id' => $salarySlip->employment_id,
                    'work_date' => $salaryDay->work_date,
                    'day_status' => $invoiceStatus,
                    'is_billable' => $isBillable,
                    'billable_units' => $units,
                    'notes' => trim(implode(' | ', array_filter([
                        $attendanceStatus ? 'Salary status: ' . $attendanceStatus : null,
                        $dayType ? 'Type: ' . $dayType : null,
                        $salaryDay->notes ?: null,
                    ]))) ?: null,
                ]);

                $created++;
            }

            if ($line) {
                $paidUnits = (float) $this->workDays()
                    ->where('client_invoice_line_id', $line->id)
                    ->where('day_status', ClientInvoiceWorkDay::STATUS_PAID)
                    ->sum('billable_units');

                $line->updateQuietly([
                    'quantity' => $paidUnits,
                    'amount' => round($paidUnits * (float) ($line->unit_rate ?? 0), 2),
                ]);
            }
        }

        $this->syncInvoiceTotalsFromLines();
        $this->recalculateContractTaxAllocationQuietly();

        if (method_exists($this, 'syncSplitDocuments')) {
            $this->syncSplitDocuments();
        }

        return [
            'salary_slips' => $salarySlips->count(),
            'work_days' => $created,
            'message' => "Generated {$created} work day(s) from {$salarySlips->count()} salary slip(s).",
        ];
    }

    public function syncInvoiceTotalsFromLines(): void
    {
        if (! $this->exists) {
            return;
        }

        $linesTotal = (float) $this->lines()->sum('amount');

        if ($linesTotal <= 0) {
            return;
        }

        $foreignPercent = (float) ($this->foreign_percentage ?? 100);
        $localPercent = (float) ($this->local_percentage ?? 0);

        if (($foreignPercent + $localPercent) <= 0) {
            $foreignPercent = 100;
            $localPercent = 0;
        }

        $foreignAmount = round($linesTotal * ($foreignPercent / 100), 2);
        $localForeignEquivalent = round($linesTotal * ($localPercent / 100), 2);
        $exchangeRate = (float) ($this->exchange_rate ?? 0);
        $localAmount = $exchangeRate > 0
            ? round($localForeignEquivalent * $exchangeRate, 2)
            : $localForeignEquivalent;

        $this->updateQuietly([
            'subtotal_amount' => $linesTotal,
            'total_amount' => $linesTotal,
            'foreign_amount_due' => $foreignAmount,
            'local_amount_due' => $localAmount,
            'local_amount_foreign_equivalent' => $localForeignEquivalent,
        ]);
    }

    public function syncSplitDocuments(): void
    {
        if (! $this->exists) {
            return;
        }

        $totalAmount = (float) ($this->total_amount ?? 0);

        $foreignCurrency = strtoupper((string) ($this->foreign_currency ?: $this->display_currency ?: 'EUR'));
        $foreignPercent = (float) ($this->foreign_percentage ?? 100);
        $foreignAmount = (float) ($this->foreign_amount_due ?? 0);

        if ($foreignAmount <= 0 && $totalAmount > 0 && $foreignPercent > 0) {
            $foreignAmount = round($totalAmount * ($foreignPercent / 100), 2);
        }

        $localCurrency = strtoupper((string) ($this->local_currency ?: 'LYD'));
        $localPercent = (float) ($this->local_percentage ?? 0);
        $localAmount = (float) ($this->local_amount_due ?? 0);

        if ($localAmount <= 0 && $totalAmount > 0 && $localPercent > 0) {
            $localAmount = round($totalAmount * ($localPercent / 100), 2);
        }

        if ($foreignPercent > 0 || $foreignAmount > 0) {
            $this->documents()->updateOrCreate(
                ['document_type' => ClientInvoiceDocument::TYPE_FOREIGN],
                [
                    'document_number' => $this->foreign_invoice_number(),
                    'currency' => $foreignCurrency,
                    'percentage' => $foreignPercent,
                    'amount' => $foreignAmount,
                    'status' => ClientInvoiceDocument::STATUS_DRAFT,
                    'snapshot' => $this->buildDocumentSnapshot(ClientInvoiceDocument::TYPE_FOREIGN, $foreignCurrency, $foreignAmount, $foreignPercent),
                ]
            );
        }

        if ($localPercent > 0 || $localAmount > 0) {
            $this->documents()->updateOrCreate(
                ['document_type' => ClientInvoiceDocument::TYPE_LOCAL],
                [
                    'document_number' => $this->local_invoice_number(),
                    'currency' => $localCurrency,
                    'percentage' => $localPercent,
                    'amount' => $localAmount,
                    'status' => ClientInvoiceDocument::STATUS_DRAFT,
                    'snapshot' => $this->buildDocumentSnapshot(ClientInvoiceDocument::TYPE_LOCAL, $localCurrency, $localAmount, $localPercent),
                ]
            );
        }
    }

    public function foreign_invoice_number(): string
    {
        return ($this->invoice_number ?: ('INV-' . $this->id)) . '-F';
    }

    public function local_invoice_number(): string
    {
        return ($this->invoice_number ?: ('INV-' . $this->id)) . '-L';
    }

    public function buildDocumentSnapshot(string $type, string $currency, float $amount, float $percentage): array
    {
        return [
            'invoice_id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'document_type' => $type,
            'currency' => $currency,
            'amount' => round($amount, 2),
            'percentage' => round($percentage, 2),
            'client_id' => $this->client_id,
            'project_id' => $this->project_id,
            'project_contract_id' => $this->project_contract_id,
            'invoice_date' => optional($this->invoice_date)->format('Y-m-d'),
            'period_start' => optional($this->period_start)->format('Y-m-d'),
            'period_end' => optional($this->period_end)->format('Y-m-d'),
            'paid_days' => method_exists($this, 'paidWorkDaysCount') ? $this->paidWorkDaysCount() : 0,
            'not_paid_days' => method_exists($this, 'notPaidWorkDaysCount') ? $this->notPaidWorkDaysCount() : 0,
            'absent_days' => method_exists($this, 'absentWorkDaysCount') ? $this->absentWorkDaysCount() : 0,
            'show_contract_tax' => (bool) ($this->show_contract_tax_on_invoice_document ?? false),
            'allocated_contract_tax_amount' => (float) ($this->allocated_contract_tax_amount ?? 0),
            'allocated_contract_tax_currency' => $this->allocated_contract_tax_currency,
        ];
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ClientInvoicePayment::class, 'client_invoice_id')
            ->orderByDesc('payment_date')
            ->orderByDesc('id');
    }

    public function totalPaidInInvoiceCurrency(): float
    {
        return round(
            $this->foreignPaidAmount() + $this->localPaidAmount(),
            2
        );
    }

    public function foreignPaidAmount(): float
    {
        return round((float) $this->payments()
            ->where('applies_to', ClientInvoicePayment::APPLIES_TO_FOREIGN)
            ->sum('amount_in_invoice_currency'), 2);
    }

    public function localPaidAmount(): float
    {
        return round((float) $this->payments()
            ->where('applies_to', ClientInvoicePayment::APPLIES_TO_LOCAL)
            ->sum('amount_in_invoice_currency'), 2);
    }

    public function foreignRemainingAmount(): float
    {
        return round(max(0, (float) ($this->foreign_amount_due ?? 0) - $this->foreignPaidAmount()), 2);
    }

    public function localRemainingAmount(): float
    {
        return round(max(0, (float) ($this->local_amount_due ?? 0) - $this->localPaidAmount()), 2);
    }

    public function remainingBalanceInInvoiceCurrency(): float
    {
        return round(max(0, (float) ($this->total_amount ?? 0) - $this->foreignPaidAmount()), 2);
    }

    public function refreshPaymentStatus(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Manual workflow protection
        |--------------------------------------------------------------------------
        | When finance/admin intentionally returns an invoice to Draft, existing
        | receipt/payment records must stay for audit but must not immediately force
        | the invoice back to Paid. Auto payment-status refresh starts only after the
        | invoice leaves Draft again.
        */
        if (in_array($this->status, [self::STATUS_DRAFT, self::STATUS_CANCELLED], true)) {
            return;
        }

        $foreignDue = round((float) ($this->foreign_amount_due ?? 0), 2);
        $localDue = round((float) ($this->local_amount_due ?? 0), 2);

        $foreignPaid = $this->foreignPaidAmount();
        $localPaid = $this->localPaidAmount();

        $foreignRemaining = round(max(0, $foreignDue - $foreignPaid), 2);
        $localRemaining = round(max(0, $localDue - $localPaid), 2);

        $hasForeignPortion = $foreignDue > 0;
        $hasLocalPortion = $localDue > 0;
        $hasAnyPayment = $foreignPaid > 0 || $localPaid > 0;

        $foreignSettled = ! $hasForeignPortion || $foreignRemaining <= 0.01;
        $localSettled = ! $hasLocalPortion || $localRemaining <= 0.01;

        if (($hasForeignPortion || $hasLocalPortion) && $foreignSettled && $localSettled) {
            $newStatus = self::STATUS_PAID;
        } elseif ($hasAnyPayment) {
            $newStatus = self::STATUS_PARTIALLY_PAID;
        } else {
            return;
        }

        if ($this->status !== $newStatus) {
            $this->updateQuietly([
                'status' => $newStatus,
            ]);
            $this->refresh();
        }
    }

    public function applyBankProfileSnapshot(?BankProfile $profile = null): void
    {
        $profile ??= $this->bankProfile;

        if (! $profile) {
            return;
        }

        $updates = [
            'bank_profile_id' => $profile->id,
            'bank_name' => $profile->bank_name,
            'swift_code' => $profile->swift_code,
        ];

        $currency = strtoupper((string) ($profile->currency ?? ''));

        if ($currency === 'USD') {
            $updates['iban_usd'] = $profile->iban;
        }

        if ($currency === 'EUR') {
            $updates['iban_eur'] = $profile->iban;
        }

        if ($currency === 'LYD') {
            $updates['iban_lyd'] = $profile->iban;
            $updates['account_number_lyd'] = $profile->account_number;
        }

        $this->fill($updates);
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_ISSUED => 'Issued',
            self::STATUS_SENT_TO_CLIENT => 'Sent to Client',
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_PARTIALLY_PAID => 'Partially Paid',
            self::STATUS_PAID => 'Paid',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function projectContract(): BelongsTo
    {
        return $this->belongsTo(ProjectContract::class);
    }


    public function resolveInvoiceAmountForContractAllocation(): float
    {
        $candidateColumns = [
            'total_amount',
            'grand_total',
            'invoice_total',
            'total',
            'subtotal',
            'amount',
            'foreign_amount',
            'local_amount',
        ];

        foreach ($candidateColumns as $column) {
            if (Schema::hasColumn($this->getTable(), $column) && filled($this->{$column} ?? null)) {
                return (float) $this->{$column};
            }
        }

        if ($this->exists && Schema::hasTable('client_invoice_lines')) {
            $lineColumns = ['line_total', 'total', 'amount', 'subtotal'];

            foreach ($lineColumns as $column) {
                if (Schema::hasColumn('client_invoice_lines', $column)) {
                    return (float) DB::table('client_invoice_lines')
                        ->where('client_invoice_id', $this->id)
                        ->sum($column);
                }
            }
        }

        return 0.0;
    }



    public function refreshContractTaxAllocation(): void
    {
        $this->applyContractTaxAllocation();
        $this->saveQuietly();
    }


    public function recalculateContractTaxAllocationQuietly(): void
    {
        if (! method_exists($this, 'applyContractTaxAllocation')) {
            return;
        }

        $this->applyContractTaxAllocation();

        if ($this->exists) {
            $this->saveQuietly();
        }
    }

    public function applyContractTaxAllocation(): void
    {
        if (! Schema::hasColumn($this->getTable(), 'project_contract_id')) {
            return;
        }

        if (! $this->project_contract_id) {
            return;
        }

        $contract = ProjectContract::find($this->project_contract_id);

        if (! $contract) {
            return;
        }

        $contractValue = (float) ($contract->contract_value ?? 0);
        $taxPaid = (float) ($contract->tax_paid ?? 0);
        $invoiceAmount = $this->resolveInvoiceAmountForContractAllocation();

        if ($contractValue <= 0 || $invoiceAmount <= 0) {
            return;
        }

        $percent = ($invoiceAmount / $contractValue) * 100;
        $allocatedTax = ($invoiceAmount / $contractValue) * $taxPaid;

        if (Schema::hasColumn($this->getTable(), 'contract_consumption_amount')) {
            $this->contract_consumption_amount = round($invoiceAmount, 2);
        }

        if (Schema::hasColumn($this->getTable(), 'contract_consumption_currency')) {
            $this->contract_consumption_currency = $this->currency ?? $contract->currency ?? null;
        }

        if (Schema::hasColumn($this->getTable(), 'contract_consumption_percent')) {
            $this->contract_consumption_percent = round($percent, 4);
        }

        if (Schema::hasColumn($this->getTable(), 'allocated_contract_tax_amount')) {
            $this->allocated_contract_tax_amount = round($allocatedTax, 2);
        }

        if (Schema::hasColumn($this->getTable(), 'allocated_contract_tax_currency')) {
            $this->allocated_contract_tax_currency = $contract->tax_currency ?? 'LYD';
        }
    }

}
