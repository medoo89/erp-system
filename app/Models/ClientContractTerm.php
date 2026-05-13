<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientContractTerm extends Model
{
    public const BILLING_DAILY_RATE = 'daily_rate';
    public const BILLING_MONTHLY_RATE = 'monthly_rate';
    public const BILLING_FIXED = 'fixed';

    protected $fillable = [
        'client_id',
        'project_id',
        'employment_id',
        'name',
        'billing_basis',
        'client_rate',
        'currency',
        'foreign_percentage',
        'local_percentage',
        'local_currency',
        'default_exchange_rate',
        'effective_from',
        'effective_to',
        'is_active',
        'is_default',
        'notes',
    ];

    protected $casts = [
        'client_rate' => 'decimal:2',
        'foreign_percentage' => 'decimal:2',
        'local_percentage' => 'decimal:2',
        'default_exchange_rate' => 'decimal:4',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function employment()
    {
        return $this->belongsTo(Employment::class, 'employment_id');
    }

    public static function billingBasisOptions(): array
    {
        return [
            self::BILLING_DAILY_RATE => 'Daily Rate',
            self::BILLING_MONTHLY_RATE => 'Monthly Rate',
            self::BILLING_FIXED => 'Fixed',
        ];
    }
}
