<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankProfileAccount extends Model
{
    protected $fillable = [
        'bank_profile_id',
        'currency',
        'account_number',
        'iban',
        'treasury_account_id',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function currencyOptions(): array
    {
        return [
            'LYD' => 'LYD',
            'USD' => 'USD',
            'EUR' => 'EUR',
            'GBP' => 'GBP',
        ];
    }

    public function bankProfile(): BelongsTo
    {
        return $this->belongsTo(BankProfile::class, 'bank_profile_id');
    }

    public function treasuryAccount(): BelongsTo
    {
        return $this->belongsTo(TreasuryAccount::class, 'treasury_account_id');
    }

    public function getDisplayNameAttribute(): string
    {
        $parts = array_filter([
            $this->bankProfile?->profile_name,
            $this->currency,
            $this->iban,
        ]);

        return implode(' - ', $parts);
    }
}
