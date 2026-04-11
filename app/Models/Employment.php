<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employment extends Model
{
    protected $fillable = [
        'pre_employment_id',
        'job_id',
        'position_title',
        'client_name',
        'project_name',
        'assigned_hr_user_id',
        'operation_officer_name',
        'employee_name',
        'employee_email',
        'employee_phone',
        'employee_code',
        'status',
        'current_work_status',
        'contract_status',
        'contract_start_date',
        'contract_end_date',
        'medical_status',
        'medical_date',
        'medical_expiry_date',
        'visa_status',
        'visa_issue_date',
        'visa_expiry_date',
        'travel_status',
        'travel_request_date',
        'rotation_status',
        'mobilization_date',
        'demobilization_date',
        'work_location',
        'rotation_pattern',
        'notes',
        'internal_notes',
        'converted_from_pre_employment_at',
    ];

    protected $casts = [
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'medical_date' => 'date',
        'medical_expiry_date' => 'date',
        'visa_issue_date' => 'date',
        'visa_expiry_date' => 'date',
        'travel_request_date' => 'date',
        'mobilization_date' => 'date',
        'demobilization_date' => 'date',
        'converted_from_pre_employment_at' => 'datetime',
    ];

    public function preEmployment()
    {
        return $this->belongsTo(PreEmployment::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function assignedHrUser()
    {
        return $this->belongsTo(User::class, 'assigned_hr_user_id');
    }

    public function files()
    {
        return $this->hasMany(EmploymentFile::class)->latest();
    }

    public function rotations()
    {
        return $this->hasMany(EmploymentRotation::class)->latest('from_date');
    }

    public function currentRotation()
    {
        return $this->hasOne(EmploymentRotation::class)
            ->where('is_current', true)
            ->latest('from_date');
    }

    public function documents()
    {
        return $this->hasMany(EmploymentDocument::class)->latest();
    }

    public function latestDocuments()
    {
        return $this->hasMany(EmploymentDocument::class)->latest('created_at');
    }
}