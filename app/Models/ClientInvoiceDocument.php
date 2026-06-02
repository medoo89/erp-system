<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientInvoiceDocument extends Model
{
    public const TYPE_FOREIGN = 'foreign';
    public const TYPE_LOCAL = 'local';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_GENERATED = 'generated';
    public const STATUS_SENT = 'sent';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'client_invoice_id',
        'document_type',
        'document_number',
        'currency',
        'percentage',
        'amount',
        'status',
        'snapshot',
        'generated_at',
        'sent_at',
        'notes',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'amount' => 'decimal:2',
        'snapshot' => 'array',
        'generated_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public static function typeOptions(): array
    {
        return [
            self::TYPE_FOREIGN => 'Foreign Currency Invoice',
            self::TYPE_LOCAL => 'Local Currency Invoice',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_GENERATED => 'Generated',
            self::STATUS_SENT => 'Sent',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(ClientInvoice::class, 'client_invoice_id');
    }
}
