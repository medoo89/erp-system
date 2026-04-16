<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = [
        'job_id',
        'full_name',
        'email',
        'phone',
        'phone_country_code',
        'phone_number',
        'whatsapp_country_code',
        'whatsapp_number',
        'cover_letter',
        'cv_path',
        'status',
        'candidate_request_status',
        'notes',

        'is_archived',
        'archive_reason',
        'archived_at',

        'decline_reason',
        'decline_notes',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
        'archived_at' => 'datetime',
    ];

    public function job()
    {
        return $this->belongsTo(\App\Models\Job::class);
    }

    public function values()
    {
        return $this->hasMany(\App\Models\JobApplicationValue::class, 'job_application_id');
    }

    public function preEmployments()
    {
        return $this->hasMany(\App\Models\PreEmployment::class);
    }

    public function candidateRequests()
    {
        return $this->hasMany(\App\Models\CandidateRequest::class)
            ->latest();
    }
}