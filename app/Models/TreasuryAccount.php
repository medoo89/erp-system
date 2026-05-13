<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreasuryAccount extends Model
{
    protected $fillable = [
        'account_name',
        'institution_name',
        'branch_name',
        'account_holder_name',
        'account_number',
        'iban',
        'swift_code',
        'account_code',
        'account_type',
        'currency',
        'opening_balance',
        'current_balance',
        'is_active',
        'is_default',
        'notes',
        'created_by',
        'updated_by',
        'bank_profile_id',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public const TYPE_CASH = 'cash';
    public const TYPE_BANK = 'bank';
    public const TYPE_CLEARING = 'clearing';

    public const CURRENCY_EUR = 'EUR';
    public const CURRENCY_USD = 'USD';
    public const CURRENCY_LYD = 'LYD';
    public const CURRENCY_GBP = 'GBP';

    public static function getAccountTypeOptions(): array
    {
        return [
            self::TYPE_CASH => 'Cash',
            self::TYPE_BANK => 'Bank',
            self::TYPE_CLEARING => 'Clearing',
        ];
    }

    public static function getCurrencyOptions(): array
    {
        return [
            self::CURRENCY_EUR => 'EUR',
            self::CURRENCY_USD => 'USD',
            self::CURRENCY_LYD => 'LYD',
            self::CURRENCY_GBP => 'GBP',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (TreasuryAccount $record) {
            if (blank($record->current_balance)) {
                $record->current_balance = $record->opening_balance ?? 0;
            }

            if ($record->is_default) {
                static::query()
                    ->where('account_type', $record->account_type)
                    ->where('currency', $record->currency)
                    ->update(['is_default' => false]);
            }

            if (auth()->check()) {
                $record->created_by = auth()->id();
                $record->updated_by = auth()->id();
            }
        });

        static::updating(function (TreasuryAccount $record) {
            if ($record->is_default) {
                static::query()
                    ->where('id', '!=', $record->id)
                    ->where('account_type', $record->account_type)
                    ->where('currency', $record->currency)
                    ->update(['is_default' => false]);
            }

            if (auth()->check()) {
                $record->updated_by = auth()->id();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function bankProfile(): BelongsTo
    {
        return $this->belongsTo(BankProfile::class, 'bank_profile_id');
    }

    public function getDisplayNameAttribute(): string
    {
        $parts = array_filter([
            $this->account_name,
            $this->institution_name,
            $this->currency,
            $this->account_type ? ucfirst($this->account_type) : null,
        ]);

        return implode(' - ', $parts);
    }
}
