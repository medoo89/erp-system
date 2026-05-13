<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BankProfile extends Model
{
    protected $fillable = [
        'profile_name',
        'beneficiary_name',
        'bank_name',
        'branch_name',
        'currency',
        'account_number',
        'iban',
        'swift_code',
        'routing_code',
        'bank_address',
        'is_default_for_invoices',
        'is_active',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_default_for_invoices' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (BankProfile $profile) {
            if ($profile->is_default_for_invoices) {
                static::query()
                    ->where('id', '!=', $profile->id)
                    ->update(['is_default_for_invoices' => false]);
            }
        });
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(BankProfileAccount::class, 'bank_profile_id')
            ->orderBy('currency');
    }

    public function activeAccounts(): HasMany
    {
        return $this->accounts()->where('is_active', true);
    }

    public function treasuryAccount(): HasOne
    {
        return $this->hasOne(TreasuryAccount::class, 'bank_profile_id')
            ->where('is_active', true)
            ->latest('id');
    }

    public function treasuryAccounts(): HasMany
    {
        return $this->hasMany(TreasuryAccount::class, 'bank_profile_id')
            ->where('is_active', true)
            ->orderBy('currency')
            ->orderBy('account_name');
    }

    public function accountForCurrency(?string $currency): ?BankProfileAccount
    {
        $currency = strtoupper((string) $currency);

        if ($currency === '') {
            return null;
        }

        if ($this->relationLoaded('accounts')) {
            return $this->accounts
                ->first(fn (BankProfileAccount $account) => strtoupper((string) $account->currency) === $currency);
        }

        return $this->accounts()
            ->where('currency', $currency)
            ->first();
    }

    public function treasuryAccountForCurrency(?string $currency): ?TreasuryAccount
    {
        $currency = strtoupper((string) $currency);

        if ($currency === '') {
            return $this->treasuryAccount()->first();
        }

        $directTreasuryAccount = $this->treasuryAccounts()
            ->where('currency', $currency)
            ->first();

        if ($directTreasuryAccount) {
            return $directTreasuryAccount;
        }

        return $this->accountForCurrency($currency)?->treasuryAccount;
    }

    public function getDisplayNameAttribute(): string
    {
        $parts = array_filter([
            $this->profile_name,
            $this->bank_name,
            $this->branch_name,
        ]);

        return implode(' - ', $parts);
    }
}
