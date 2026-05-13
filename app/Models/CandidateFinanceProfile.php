<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateFinanceProfile extends Model
{
    public const BASIS_DAILY_RATE = 'daily_rate';
    public const BASIS_MONTHLY = 'monthly';

    protected $fillable = [
        'job_application_id',
        'pre_employment_id',
        'employment_id',
        'job_id',
        'client_id',
        'project_id',
        'source_candidate_request_id',
        'finance_status',
        'salary_basis',
        'agreed_salary_amount',
        'agreed_salary_currency',
        'daily_rate',
        'monthly_salary',
        'payout_currency',
        'client_billing_basis',
        'client_billing_rate',
        'client_billing_currency',
        'source_type',
        'effective_from',
        'effective_to',
        'is_current',
        'is_hidden_from_non_finance',
        'finance_notes',
    ];

    protected $casts = [
        'agreed_salary_amount' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'monthly_salary' => 'decimal:2',
        'client_billing_rate' => 'decimal:2',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_current' => 'boolean',
        'is_hidden_from_non_finance' => 'boolean',
    ];

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

    public function resolvedSalaryBasis(): ?string
    {
        return $this->salary_basis;
    }

    public function resolvedDailyRate(): float
    {
        return (float) ($this->daily_rate ?? 0);
    }

    public function resolvedMonthlySalary(): float
    {
        return (float) ($this->monthly_salary ?? 0);
    }

    public function resolvedPayoutCurrency(): ?string
    {
        return $this->payout_currency ?: $this->agreed_salary_currency;
    }

    public function resolvedClientBillingBasis(): string
    {
        return $this->client_billing_basis ?: ($this->salary_basis ?: self::BASIS_DAILY_RATE);
    }

    public function resolvedClientBillingRate(): float
    {
        return (float) ($this->client_billing_rate ?? 0);
    }

    public function resolvedClientBillingCurrency(): ?string
    {
        return $this->client_billing_currency ?: $this->resolvedPayoutCurrency();
    }
}