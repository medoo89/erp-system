<?php

namespace App\Models;

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
    ];

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
    ];

    protected static function booted(): void
    {
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
        if ($this->status === self::STATUS_CANCELLED) {
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
}
