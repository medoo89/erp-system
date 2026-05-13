<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalTimelineEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'portal_account_id',
        'event_type',
        'title',
        'description',
        'event_date',
        'badge_status',
        'related_type',
        'related_id',
        'visible_to_user',
        'created_by',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'visible_to_user' => 'boolean',
    ];

    public function portalAccount()
    {
        return $this->belongsTo(PortalAccount::class);
    }
}
