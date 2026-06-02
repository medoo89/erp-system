<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientInvoiceWorkDay extends Model
{
    public const STATUS_PAID = 'paid';
    public const STATUS_NOT_PAID = 'not_paid';
    public const STATUS_ABSENT = 'absent';

    protected $fillable = [
        'client_invoice_id',
        'client_invoice_line_id',
        'employment_id',
        'work_date',
        'day_status',
        'is_billable',
        'billable_units',
        'notes',
    ];

    protected $casts = [
        'work_date' => 'date',
        'is_billable' => 'boolean',
        'billable_units' => 'decimal:2',
    ];

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PAID => 'Paid Day',
            self::STATUS_NOT_PAID => 'Not Paid',
            self::STATUS_ABSENT => 'Absent',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(ClientInvoice::class, 'client_invoice_id');
    }

    public function line(): BelongsTo
    {
        return $this->belongsTo(ClientInvoiceLine::class, 'client_invoice_line_id');
    }

    public function employment(): BelongsTo
    {
        return $this->belongsTo(Employment::class, 'employment_id');
    }
}
