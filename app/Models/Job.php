<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'job_openings';

    protected $fillable = [
        'title',
        'department',
        'location',
        'employment_type',
        'description',
        'requirements',
        'is_active',
        'closing_date',
        'template_id',
        'project_id',

        // archive fields
        'is_archived',
        'archive_reason',
        'archived_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_archived' => 'boolean',
        'closing_date' => 'date',
        'archived_at' => 'datetime',
    ];

    public function applications()
    {
        return $this->hasMany(\App\Models\JobApplication::class, 'job_id');
    }

    public function applicationFields()
    {
        return $this->hasMany(\App\Models\JobApplicationField::class, 'job_id')
            ->orderBy('sort_order');
    }

    public function template()
    {
        return $this->belongsTo(\App\Models\JobApplicationTemplate::class, 'template_id', 'id');
    }

    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class, 'project_id');
    }

    public function isClosed(): bool
    {
        return filled($this->closing_date) && $this->closing_date->lt(today());
    }

    public function isPubliclyVisible(): bool
    {
        return $this->is_active && ! $this->is_archived;
    }

    public function canAcceptApplications(): bool
    {
        return $this->isPubliclyVisible() && ! $this->isClosed();
    }
}