<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'portal_account_id',
        'category',
        'title',
        'message',
        'action_type',
        'action_url',
        'action_label',
        'related_type',
        'related_id',
        'is_read',
        'read_at',
        'emailed_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'emailed_at' => 'datetime',
    ];

    public function portalAccount()
    {
        return $this->belongsTo(PortalAccount::class);
    }
}
