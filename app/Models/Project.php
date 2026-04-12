<?php

namespace App\Models;

use App\Services\CodeGeneratorService;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'client_id',
        'name',
        'project_code',
        'code',
        'location',
        'description',
        'notes',
        'is_active',
        'is_archived',
        'archive_reason',
        'archived_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_archived' => 'boolean',
        'archived_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $project) {
            if (blank($project->project_code) && filled($project->name)) {
                $project->project_code = app(CodeGeneratorService::class)
                    ->generateProjectCode($project->name, $project->client_id);
            }
        });

        static::updating(function (self $project) {
            if (blank($project->project_code) && filled($project->name)) {
                $project->project_code = app(CodeGeneratorService::class)
                    ->generateProjectCode($project->name, $project->client_id, $project->id);
            }
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }
}