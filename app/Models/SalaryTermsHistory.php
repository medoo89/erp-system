<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryTermsHistory extends Model
{
    protected $table = 'salary_terms_history';

    protected $fillable = [
        'candidate_finance_profile_id',
        'job_application_id',
        'pre_employment_id',
        'employment_id',
        'job_id',
        'client_id',
        'project_id',
        'source_candidate_request_id',
        'created_by',
        'source_type',
        'change_reason',
        'salary_basis',
        'amount',
        'currency',
        'daily_rate',
        'monthly_salary',
        'effective_from',
        'effective_to',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'monthly_salary' => 'decimal:2',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public function financeProfile()
    {
        return $this->belongsTo(CandidateFinanceProfile::class, 'candidate_finance_profile_id');
    }

    public function jobApplication()
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function preEmployment()
    {
        return $this->belongsTo(PreEmployment::class, 'pre_employment_id');
    }

    public function employment()
    {
        return $this->belongsTo(Employment::class, 'employment_id');
    }

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function sourceCandidateRequest()
    {
        return $this->belongsTo(CandidateRequest::class, 'source_candidate_request_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}