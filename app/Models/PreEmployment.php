<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PreEmployment extends Model
{
    protected $fillable = [
        'job_application_id',
        'job_id',
        'candidate_name',
        'candidate_email',
        'candidate_phone',
        'status',
        'portal_token',
        'availability_date',
        'expected_rate',
        'final_rate',
        'contract_status',
        'medical_status',
        'visa_status',
        'travel_status',
        'assigned_hr_user_id',
        'notes',
        'internal_notes',
        'is_declined',
        'decline_reason',
        'decline_notes',
        'declined_at',
        'is_archived',
        'archive_reason',
        'archived_at',
        'converted_to_employment_at',
    ];

    protected $casts = [
        'availability_date' => 'date',
        'is_declined' => 'boolean',
        'declined_at' => 'datetime',
        'is_archived' => 'boolean',
        'archived_at' => 'datetime',
        'converted_to_employment_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $preEmployment) {
            if (blank($preEmployment->portal_token)) {
                $preEmployment->portal_token = Str::random(64);
            }
        });
    }

    public function jobApplication()
    {
        return $this->belongsTo(JobApplication::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function assignedHrUser()
    {
        return $this->belongsTo(User::class, 'assigned_hr_user_id');
    }

    public function requirements()
    {
        return $this->hasMany(PreEmploymentRequirement::class);
    }

    public function uploads()
    {
        return $this->hasMany(PreEmploymentUpload::class);
    }
}