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
        'portal_status',
        'portal_last_sent_at',
        'portal_last_submitted_at',
        'availability_date',
        'expected_rate',
        'final_rate',
        'contract_status',
        'medical_status',
        'visa_status',
        'travel_status',
        'caf_status',
        'caf_file_path',
        'gl_status',
        'gl_file_path',
        'client_tracking_notes',
        'candidate_tracking_notes',
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
        'portal_last_sent_at' => 'datetime',
        'portal_last_submitted_at' => 'datetime',
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

            if (blank($preEmployment->portal_status)) {
                $preEmployment->portal_status = 'not_sent';
            }
        });
    }

    public function jobApplication()
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
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

    public function portalFields()
    {
        return $this->hasMany(PreEmploymentPortalField::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function portalValues()
    {
        return $this->hasMany(PreEmploymentPortalValue::class);
    }

    public function files()
    {
        return $this->hasMany(PreEmploymentFile::class)
            ->latest();
    }
}