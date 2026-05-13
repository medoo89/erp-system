<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TreasuryOperation extends Model
{
    protected $fillable = [
        'operation_no',
        'operation_type',
        'source_account_id',
        'destination_account_id',
        'clearing_account_id',
        'amount',
        'currency',
        'fee_amount',
        'fee_account_id',
        'business_status',
        'settlement_status',
        'operation_date',
        'cleared_at',
        'rejected_at',
        'reference_type',
        'reference_id',
        'description',
        'notes',
        'created_by',
        'updated_by',
            'from_account_id',
        'to_account_id',
        'from_amount',
        'to_amount',
        'from_currency',
        'to_currency',
        'exchange_rate',
        'is_posted',
];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee_amount' => 'decimal:2',
        'operation_date' => 'date',
        'cleared_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public const TYPE_SALARY_BANK_TRANSFER = 'salary_bank_transfer';
    public const TYPE_INVOICE_RECEIPT = 'invoice_receipt';
    public const TYPE_INTERNAL_TRANSFER = 'internal_transfer';
    public const TYPE_EXPENSE_PAYMENT = 'expense_payment';
    public const TYPE_REIMBURSEMENT = 'reimbursement';
    public const TYPE_ADJUSTMENT = 'adjustment';
    public const TYPE_REVERSAL = 'reversal';

    public const BUSINESS_DRAFT = 'draft';
    public const BUSINESS_APPROVED = 'approved';
    public const BUSINESS_SENT = 'sent';
    public const BUSINESS_RECEIVED = 'received';
    public const BUSINESS_PAID = 'paid';
    public const BUSINESS_REJECTED = 'rejected';
    public const BUSINESS_CANCELLED = 'cancelled';

    public const SETTLEMENT_PENDING = 'pending';
    public const SETTLEMENT_CLEARED = 'cleared';
    public const SETTLEMENT_FAILED = 'failed';
    public const SETTLEMENT_REVERSED = 'reversed';

    public static function getOperationTypeOptions(): array
    {
        return [
            self::TYPE_SALARY_BANK_TRANSFER => 'Salary Bank Transfer',
            self::TYPE_INVOICE_RECEIPT => 'Invoice Receipt',
            self::TYPE_INTERNAL_TRANSFER => 'Internal Transfer',
            self::TYPE_EXPENSE_PAYMENT => 'Expense Payment',
            self::TYPE_REIMBURSEMENT => 'Reimbursement',
            self::TYPE_ADJUSTMENT => 'Adjustment',
            self::TYPE_REVERSAL => 'Reversal',
        ];
    }

    public static function getBusinessStatusOptions(): array
    {
        return [
            self::BUSINESS_DRAFT => 'Draft',
            self::BUSINESS_APPROVED => 'Approved',
            self::BUSINESS_SENT => 'Sent',
            self::BUSINESS_RECEIVED => 'Received',
            self::BUSINESS_PAID => 'Paid',
            self::BUSINESS_REJECTED => 'Rejected',
            self::BUSINESS_CANCELLED => 'Cancelled',
        ];
    }

    public static function getSettlementStatusOptions(): array
    {
        return [
            self::SETTLEMENT_PENDING => 'Pending',
            self::SETTLEMENT_CLEARED => 'Cleared',
            self::SETTLEMENT_FAILED => 'Failed',
            self::SETTLEMENT_REVERSED => 'Reversed',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (TreasuryOperation $record) {
            if (blank($record->operation_no)) {
                $record->operation_no = 'TOP-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
            }

            $record->currency = strtoupper((string) ($record->currency ?: 'USD'));
            $record->fee_amount = (float) ($record->fee_amount ?? 0);

            if (blank($record->business_status)) {
                $record->business_status = self::BUSINESS_DRAFT;
            }

            if (blank($record->settlement_status)) {
                $record->settlement_status = self::SETTLEMENT_PENDING;
            }

            if (blank($record->operation_date)) {
                $record->operation_date = now()->toDateString();
            }

            if (auth()->check()) {
                $record->created_by = auth()->id();
                $record->updated_by = auth()->id();
            }
        });

        static::updating(function (TreasuryOperation $record) {
            $record->currency = strtoupper((string) ($record->currency ?: 'USD'));
            $record->fee_amount = (float) ($record->fee_amount ?? 0);

            if (auth()->check()) {
                $record->updated_by = auth()->id();
            }
        });
    }

    public function sourceAccount(): BelongsTo
    {
        return $this->belongsTo(TreasuryAccount::class, 'source_account_id');
    }

    public function destinationAccount(): BelongsTo
    {
        return $this->belongsTo(TreasuryAccount::class, 'destination_account_id');
    }

    public function clearingAccount(): BelongsTo
    {
        return $this->belongsTo(TreasuryAccount::class, 'clearing_account_id');
    }

    public function feeAccount(): BelongsTo
    {
        return $this->belongsTo(TreasuryAccount::class, 'fee_account_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(TreasuryTransaction::class, 'treasury_operation_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
