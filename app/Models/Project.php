<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'client_id',
        'name',
        'code',
        'location',
        'site_type',
        'status',
        'start_date',
        'end_date',
        'description',
        'notes',
        'is_active',
        'is_archived',
        'archive_reason',
        'archived_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_archived' => 'boolean',
        'archived_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'project_id');
    }
}