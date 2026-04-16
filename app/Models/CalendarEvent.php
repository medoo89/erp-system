<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarEvent extends Model
{
    protected $fillable = [
        'title',
        'event_type',
        'notes',
        'event_date',
        'is_all_day',
        'color',
        'linked_type',
        'linked_id',
        'job_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_all_day' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}