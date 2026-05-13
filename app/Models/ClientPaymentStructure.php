<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientPaymentStructure extends Model
{
    public const FOREIGN_CURRENCY_USD = 'USD';
    public const FOREIGN_CURRENCY_EUR = 'EUR';
    public const FOREIGN_CURRENCY_GBP = 'GBP';

    public const FOREIGN_CURRENCIES = [
        self::FOREIGN_CURRENCY_USD,
        self::FOREIGN_CURRENCY_EUR,
        self::FOREIGN_CURRENCY_GBP,
    ];

    public const CONVERSION_FIXED_CONTRACT_SPLIT = 'fixed_contract_split';
    public const CONVERSION_MANUAL_RATE = 'manual_rate_conversion';

    public const CONVERSION_MODES = [
        self::CONVERSION_FIXED_CONTRACT_SPLIT,
        self::CONVERSION_MANUAL_RATE,
    ];

    protected $fillable = [
        'client_id',
        'project_id',
        'name',
        'allow_dual_currency',
        'foreign_currency',
        'foreign_percentage',
        'lyd_percentage',
        'lyd_conversion_mode',
        'manual_exchange_rate',
        'effective_from',
        'effective_to',
        'is_default',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'allow_dual_currency' => 'boolean',
        'foreign_percentage' => 'decimal:2',
        'lyd_percentage' => 'decimal:2',
        'manual_exchange_rate' => 'decimal:4',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function isValidSplit(): bool
    {
        return ((float) $this->foreign_percentage + (float) $this->lyd_percentage) === 100.0;
    }

    public function isDualCurrency(): bool
    {
        return (bool) $this->allow_dual_currency;
    }

    public static function foreignCurrencyLabels(): array
    {
        return [
            self::FOREIGN_CURRENCY_USD => 'USD',
            self::FOREIGN_CURRENCY_EUR => 'EUR',
            self::FOREIGN_CURRENCY_GBP => 'GBP',
        ];
    }

    public static function conversionModeLabels(): array
    {
        return [
            self::CONVERSION_FIXED_CONTRACT_SPLIT => 'Fixed Contract Split',
            self::CONVERSION_MANUAL_RATE => 'Manual Rate Conversion',
        ];
    }
}