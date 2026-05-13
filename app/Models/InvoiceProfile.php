<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceProfile extends Model
{
    protected $fillable = [
        'name',
        'currency',
        'bank_name',
        'swift_code',
        'account_number_lyd',
        'iban_lyd',
        'iban_usd',
        'iban_eur',
        'terms_text',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function clientInvoices()
    {
        return $this->hasMany(ClientInvoice::class, 'invoice_profile_id');
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
}
