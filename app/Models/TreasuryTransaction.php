<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TreasuryTransaction extends Model
{
    protected $fillable = [
        'transaction_no',
        'treasury_account_id',
        'treasury_operation_id',
        'transaction_type',
        'direction',
        'amount',
        'currency',
        'transaction_date',
        'client_id',
        'project_id',
        'employment_id',
        'reference_type',
        'reference_id',
        'description',
        'notes',
        'is_posted',
        'settlement_status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'is_posted' => 'boolean',
    ];

    public const DIRECTION_IN = 'in';
    public const DIRECTION_OUT = 'out';

    public const TYPE_INVOICE_PAYMENT = 'invoice_payment';
    public const TYPE_SALARY_PAYMENT = 'salary_payment';
    public const TYPE_EXPENSE_PAYMENT = 'expense_payment';
    public const TYPE_TRANSFER_IN = 'transfer_in';
    public const TYPE_TRANSFER_OUT = 'transfer_out';
    public const TYPE_ADJUSTMENT = 'adjustment';
    public const TYPE_BANK_FEE = 'bank_fee';
    public const TYPE_DEDUCTION = 'deduction';
    public const TYPE_MANUAL = 'manual';
    public const TYPE_CLEARING_IN = 'clearing_in';
    public const TYPE_CLEARING_OUT = 'clearing_out';

    public const SETTLEMENT_PENDING = 'pending';
    public const SETTLEMENT_CLEARED = 'cleared';
    public const SETTLEMENT_FAILED = 'failed';
    public const SETTLEMENT_REVERSED = 'reversed';

    public static function getDirectionOptions(): array
    {
        return [
            self::DIRECTION_IN => 'Incoming',
            self::DIRECTION_OUT => 'Outgoing',
        ];
    }

    public static function getTransactionTypeOptions(): array
    {
        return [
            self::TYPE_INVOICE_PAYMENT => 'Invoice Payment',
            self::TYPE_SALARY_PAYMENT => 'Salary Payment',
            self::TYPE_EXPENSE_PAYMENT => 'Expense Payment',
            self::TYPE_TRANSFER_IN => 'Transfer In',
            self::TYPE_TRANSFER_OUT => 'Transfer Out',
            self::TYPE_ADJUSTMENT => 'Adjustment',
            self::TYPE_BANK_FEE => 'Bank Fee',
            self::TYPE_DEDUCTION => 'Deduction',
            self::TYPE_MANUAL => 'Manual',
            self::TYPE_CLEARING_IN => 'Clearing In',
            self::TYPE_CLEARING_OUT => 'Clearing Out',
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
        static::creating(function (TreasuryTransaction $record) {
            if (blank($record->transaction_no)) {
                $record->transaction_no = static::generateTransactionNo();
            }

            if (blank($record->currency) && $record->treasuryAccount) {
                $record->currency = $record->treasuryAccount->currency;
            }

            if (blank($record->settlement_status)) {
                $record->settlement_status = self::SETTLEMENT_CLEARED;
            }

            if (auth()->check()) {
                $record->created_by = auth()->id();
                $record->updated_by = auth()->id();
            }
        });

        static::updating(function (TreasuryTransaction $record) {
            if (auth()->check()) {
                $record->updated_by = auth()->id();
            }
        });

        static::created(function (TreasuryTransaction $record) {
            static::recalculateAccountBalance($record->treasury_account_id);
        });

        static::updated(function (TreasuryTransaction $record) {
            static::recalculateAccountBalance($record->treasury_account_id);

            if ($record->wasChanged('treasury_account_id')) {
                $old = $record->getOriginal('treasury_account_id');
                if ($old) {
                    static::recalculateAccountBalance($old);
                }
            }
        });

        static::deleted(function (TreasuryTransaction $record) {
            static::recalculateAccountBalance($record->treasury_account_id);
        });
    }

    public static function generateTransactionNo(): string
    {
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(4));

        return 'TRX-' . $date . '-' . $random;
    }

    public static function recalculateAccountBalance(?int $accountId): void
    {
        if (! $accountId) {
            return;
        }

        $account = TreasuryAccount::find($accountId);

        if (! $account) {
            return;
        }

        $incoming = static::query()
            ->where('treasury_account_id', $accountId)
            ->where('is_posted', true)
            ->where('direction', self::DIRECTION_IN)
            ->sum('amount');

        $outgoing = static::query()
            ->where('treasury_account_id', $accountId)
            ->where('is_posted', true)
            ->where('direction', self::DIRECTION_OUT)
            ->sum('amount');

        $account->current_balance = ((float) $account->opening_balance) + ((float) $incoming) - ((float) $outgoing);
        $account->saveQuietly();
    }

    public function treasuryAccount(): BelongsTo
    {
        return $this->belongsTo(TreasuryAccount::class, 'treasury_account_id');
    }

    public function treasuryOperation(): BelongsTo
    {
        return $this->belongsTo(TreasuryOperation::class, 'treasury_operation_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function employment(): BelongsTo
    {
        return $this->belongsTo(Employment::class);
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
