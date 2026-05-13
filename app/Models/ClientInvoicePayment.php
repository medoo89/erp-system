<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class ClientInvoicePayment extends Model
{
    public const APPLIES_TO_FOREIGN = 'foreign';
    public const APPLIES_TO_LOCAL = 'local';

    public const SETTLEMENT_PENDING = 'pending';
    public const SETTLEMENT_CLEARED = 'cleared';
    public const SETTLEMENT_FAILED = 'failed';
    public const SETTLEMENT_REVERSED = 'reversed';

    protected $fillable = [
        'client_invoice_id',
        'treasury_account_id',
        'treasury_operation_id',
        'treasury_transaction_id',
        'bank_profile_id',
        'payment_date',
        'amount',
        'currency',
        'applies_to',
        'exchange_rate',
        'amount_in_invoice_currency',
        'reference_no',
        'attachment_path',
        'settlement_status',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'amount_in_invoice_currency' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (ClientInvoicePayment $payment) {
            $payment->amount = (float) ($payment->amount ?? 0);
            $payment->applies_to = $payment->applies_to ?: self::APPLIES_TO_FOREIGN;
            $payment->amount_in_invoice_currency = $payment->resolveInvoiceCurrencyAmount();

            if (blank($payment->settlement_status)) {
                $payment->settlement_status = self::SETTLEMENT_CLEARED;
            }

            if (! $payment->treasury_account_id) {
                throw ValidationException::withMessages([
                    'treasury_account_id' => 'Treasury Account is required for invoice receipt posting.',
                ]);
            }
        });

        static::saved(function (ClientInvoicePayment $payment) {
            $payment->syncTreasuryPosting();
            $payment->clientInvoice?->refreshPaymentStatus();
        });

        static::deleted(function (ClientInvoicePayment $payment) {
            $payment->deleteTreasuryPosting();
            $payment->clientInvoice?->refreshPaymentStatus();
        });
    }

    public function clientInvoice(): BelongsTo
    {
        return $this->belongsTo(ClientInvoice::class, 'client_invoice_id');
    }

    public function treasuryAccount(): BelongsTo
    {
        return $this->belongsTo(TreasuryAccount::class, 'treasury_account_id');
    }

    public function treasuryOperation(): BelongsTo
    {
        return $this->belongsTo(TreasuryOperation::class, 'treasury_operation_id');
    }

    public function treasuryTransaction(): BelongsTo
    {
        return $this->belongsTo(TreasuryTransaction::class, 'treasury_transaction_id');
    }

    public function bankProfile(): BelongsTo
    {
        return $this->belongsTo(BankProfile::class, 'bank_profile_id');
    }

    public function resolveInvoiceCurrencyAmount(): float
    {
        $amount = (float) ($this->amount ?? 0);
        $exchangeRate = (float) ($this->exchange_rate ?? 0);
        $paymentCurrency = strtoupper((string) ($this->currency ?? ''));
        $invoice = $this->clientInvoice;

        if (! $invoice || $amount <= 0) {
            return 0;
        }

        if ($this->applies_to === self::APPLIES_TO_LOCAL) {
            $targetCurrency = strtoupper((string) ($invoice->local_currency ?: ''));
        } else {
            $targetCurrency = strtoupper((string) ($invoice->foreign_currency ?: $invoice->display_currency ?: ''));
        }

        if ($paymentCurrency === '' || $targetCurrency === '' || $paymentCurrency === $targetCurrency) {
            return round($amount, 2);
        }

        if ($exchangeRate > 0) {
            return round($amount / $exchangeRate, 2);
        }

        return round($amount, 2);
    }

    public static function currencyOptions(): array
    {
        return [
            'USD' => 'USD',
            'EUR' => 'EUR',
            'GBP' => 'GBP',
            'LYD' => 'LYD',
        ];
    }

    public static function appliesToOptions(): array
    {
        return [
            self::APPLIES_TO_FOREIGN => 'Foreign Portion',
            self::APPLIES_TO_LOCAL => 'Local Portion',
        ];
    }

    public static function settlementStatusOptions(): array
    {
        return [
            self::SETTLEMENT_PENDING => 'Pending',
            self::SETTLEMENT_CLEARED => 'Cleared',
            self::SETTLEMENT_FAILED => 'Failed',
            self::SETTLEMENT_REVERSED => 'Reversed',
        ];
    }

    public function isPending(): bool
    {
        return $this->settlement_status === self::SETTLEMENT_PENDING;
    }

    public function isCleared(): bool
    {
        return $this->settlement_status === self::SETTLEMENT_CLEARED;
    }

    public function syncTreasuryPosting(): void
    {
        $invoice = $this->clientInvoice;

        if (! $invoice || ! $this->treasury_account_id) {
            return;
        }

        $transaction = TreasuryTransaction::query()->firstOrNew([
            'reference_type' => 'invoice_payment',
            'reference_id' => $this->id,
        ]);

        $transaction->treasury_account_id = $this->treasury_account_id;
        $transaction->treasury_operation_id = $this->treasury_operation_id;
        $transaction->transaction_type = TreasuryTransaction::TYPE_INVOICE_PAYMENT;
        $transaction->direction = TreasuryTransaction::DIRECTION_IN;
        $transaction->amount = $this->amount;
        $transaction->currency = $this->currency ?: ($invoice->display_currency ?: 'USD');
        $transaction->transaction_date = $this->payment_date ?: now()->toDateString();
        $transaction->client_id = $invoice->client_id;
        $transaction->project_id = $invoice->project_id;
        $transaction->employment_id = null;
        $transaction->description = $this->isPending()
            ? 'Invoice Receipt Pending ' . ($invoice->invoice_number ?: ('#' . $invoice->id))
            : 'Invoice Receipt ' . ($invoice->invoice_number ?: ('#' . $invoice->id));
        $transaction->notes = $this->notes;
        $transaction->is_posted = true;
        $transaction->settlement_status = $this->settlement_status ?: self::SETTLEMENT_CLEARED;
        $transaction->save();

        if ((int) $this->treasury_transaction_id !== (int) $transaction->id) {
            $this->treasury_transaction_id = $transaction->id;
            $this->saveQuietly();
        }
    }

    public function settleFromClearingToAccount(int $destinationAccountId, ?string $settlementDate = null, ?string $notes = null): void
    {
        if (! $this->isPending()) {
            throw ValidationException::withMessages([
                'settlement_status' => 'Only pending invoice receipts can be settled from clearing.',
            ]);
        }

        $sourceAccount = $this->treasuryAccount;
        $destinationAccount = TreasuryAccount::query()->find($destinationAccountId);

        if (! $sourceAccount) {
            throw ValidationException::withMessages([
                'treasury_account_id' => 'Source clearing account could not be found.',
            ]);
        }

        if (! $destinationAccount) {
            throw ValidationException::withMessages([
                'destination_account_id' => 'Destination treasury account could not be found.',
            ]);
        }

        if ($sourceAccount->account_type !== TreasuryAccount::TYPE_CLEARING) {
            throw ValidationException::withMessages([
                'treasury_account_id' => 'Settlement source must be a clearing account.',
            ]);
        }

        $date = $settlementDate ?: now()->toDateString();

        $operation = TreasuryOperation::query()->create([
            'operation_no' => method_exists(TreasuryOperation::class, 'generateOperationNo')
                ? TreasuryOperation::generateOperationNo()
                : ('TOP-' . now()->format('Ymd-His')),
            'operation_type' => TreasuryOperation::TYPE_INVOICE_RECEIPT,
            'source_account_id' => $sourceAccount->id,
            'from_account_id' => $sourceAccount->id,
            'destination_account_id' => $destinationAccount->id,
            'to_account_id' => $destinationAccount->id,
            'clearing_account_id' => $sourceAccount->id,
            'amount' => $this->amount,
            'from_amount' => $this->amount,
            'to_amount' => $this->amount,
            'currency' => $this->currency ?: $sourceAccount->currency ?: 'USD',
            'from_currency' => $this->currency ?: $sourceAccount->currency ?: 'USD',
            'to_currency' => $this->currency ?: $destinationAccount->currency ?: $sourceAccount->currency ?: 'USD',
            'exchange_rate' => 1,
            'fee_amount' => 0,
            'business_status' => TreasuryOperation::BUSINESS_RECEIVED,
            'settlement_status' => TreasuryOperation::SETTLEMENT_CLEARED,
            'operation_date' => $date,
            'cleared_at' => now(),
            'reference_type' => 'client_invoice_payment_settlement',
            'reference_id' => $this->id,
            'description' => 'Invoice receipt settlement from clearing',
            'notes' => $notes,
        ]);

        TreasuryTransaction::query()->create([
            'treasury_account_id' => $sourceAccount->id,
            'treasury_operation_id' => $operation->id,
            'transaction_type' => TreasuryTransaction::TYPE_CLEARING_OUT,
            'direction' => TreasuryTransaction::DIRECTION_OUT,
            'amount' => $this->amount,
            'currency' => $this->currency ?: $sourceAccount->currency ?: 'USD',
            'transaction_date' => $date,
            'client_id' => $this->clientInvoice?->client_id,
            'project_id' => $this->clientInvoice?->project_id,
            'employment_id' => null,
            'reference_type' => 'client_invoice_payment_settlement',
            'reference_id' => $this->id,
            'description' => 'Invoice settlement out from clearing',
            'notes' => $notes,
            'is_posted' => true,
            'settlement_status' => TreasuryTransaction::SETTLEMENT_CLEARED,
        ]);

        TreasuryTransaction::query()->create([
            'treasury_account_id' => $destinationAccount->id,
            'treasury_operation_id' => $operation->id,
            'transaction_type' => TreasuryTransaction::TYPE_TRANSFER_IN,
            'direction' => TreasuryTransaction::DIRECTION_IN,
            'amount' => $this->amount,
            'currency' => $this->currency ?: $destinationAccount->currency ?: 'USD',
            'transaction_date' => $date,
            'client_id' => $this->clientInvoice?->client_id,
            'project_id' => $this->clientInvoice?->project_id,
            'employment_id' => null,
            'reference_type' => 'client_invoice_payment_settlement',
            'reference_id' => $this->id,
            'description' => 'Invoice settlement into final account',
            'notes' => $notes,
            'is_posted' => true,
            'settlement_status' => TreasuryTransaction::SETTLEMENT_CLEARED,
        ]);

        $this->settlement_status = self::SETTLEMENT_CLEARED;
        $this->notes = trim(implode("\n", array_filter([
            $this->notes,
            'Settlement confirmed from clearing to account #' . $destinationAccount->id . '.',
            $notes,
        ])));
        $this->saveQuietly();

        $this->clientInvoice?->refreshPaymentStatus();
    }

    public function deleteTreasuryPosting(): void
    {
        /*
         * Critical launch rule:
         * When an invoice receipt/payment is removed, it must disappear from:
         * - invoice payment records
         * - treasury transactions
         * - treasury operations
         * - global finance totals
         */

        $paymentId = (int) $this->id;

        if ($paymentId <= 0) {
            return;
        }

        // Delete direct receipt transaction.
        if ($this->treasury_transaction_id) {
            TreasuryTransaction::query()
                ->where('id', $this->treasury_transaction_id)
                ->delete();
        }

        TreasuryTransaction::query()
            ->where('reference_type', 'invoice_payment')
            ->where('reference_id', $paymentId)
            ->delete();

        // Delete settlement transactions created when moving from clearing to final bank/cash.
        TreasuryTransaction::query()
            ->where('reference_type', 'client_invoice_payment_settlement')
            ->where('reference_id', $paymentId)
            ->delete();

        // Delete settlement operations created for this receipt.
        TreasuryOperation::query()
            ->where('reference_type', 'client_invoice_payment_settlement')
            ->where('reference_id', $paymentId)
            ->delete();

        if ($this->treasury_transaction_id !== null || $this->treasury_operation_id !== null) {
            $this->treasury_transaction_id = null;
            $this->treasury_operation_id = null;
            $this->saveQuietly();
        }
    }
}
