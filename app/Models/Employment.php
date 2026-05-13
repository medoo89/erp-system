<?php

namespace App\Models;

use App\Services\CodeGeneratorService;
use App\Services\EmployeePortalProvisioningService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Employment extends Model
{
    protected $fillable = [
        'pre_employment_id',
        'job_id',
        'is_open_ended_contract',
        'contract_type',
        'office_employee_type',
        'office_department',
        'employee_category',
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
        'portal_access_enabled',
        'portal_status',
        'portal_disabled_reason',
        'portal_disabled_at',
        'password_setup_sent_at',
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
        'is_open_ended_contract' => 'boolean',
        'portal_access_enabled' => 'boolean',
        'portal_disabled_at' => 'datetime',
        'password_setup_sent_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $employment) {
            if (filled($employment->employee_code)) {
                return;
            }

            if ($employment->pre_employment_id) {
                $preEmployment = PreEmployment::find($employment->pre_employment_id);

                if ($preEmployment?->employee_code) {
                    $employment->employee_code = $preEmployment->employee_code;
                }
            }

            if (blank($employment->employee_code) && $employment->job_id) {
                $job = Job::with('project.client')->find($employment->job_id);

                if (! $job) {
                    return;
                }

                $clientCode = $job->project?->client?->code;
                $projectCode = $job->project?->project_code ?: $job->project?->code;

                if (blank($projectCode) && filled($job->project?->name)) {
                    $projectCode = app(CodeGeneratorService::class)
                        ->generateProjectCode($job->project->name, $job->project->client_id, $job->project->id);

                    $job->project->project_code = $projectCode;
                    $job->project->save();
                }

                if ($clientCode && $projectCode) {
                    $employment->employee_code = app(CodeGeneratorService::class)
                        ->generateEmployeeCode($clientCode, $projectCode);
                }

                $employment->position_title = $employment->position_title ?: $job->title;
                $employment->project_name = $employment->project_name ?: $job->project?->name;
                $employment->client_name = $employment->client_name ?: $job->project?->client?->name;
            }
        });

        static::saved(function (self $employment) {
            if (blank($employment->employee_email)) {
                return;
            }

            app(EmployeePortalProvisioningService::class)
                ->syncForEmployment($employment, false);
        });
    }



    public function isOfficeEmployee(): bool
    {
        return ($this->employee_category ?: 'operational') === 'office';
    }

    public function isOperationalEmployee(): bool
    {
        return ($this->employee_category ?: 'operational') === 'operational';
    }

    public function employeeCategoryLabel(): string
    {
        return match ($this->employee_category ?: 'operational') {
            'office' => 'Office Employee',
            'operational' => 'Operational Employee',
            default => ucfirst(str_replace('_', ' ', (string) $this->employee_category)),
        };
    }

    public function portalUser()
    {
        return $this->hasOne(User::class, 'employment_id');
    }

    public function shouldBlockPortalAccess(): bool
    {
        $values = collect([
            $this->status,
            $this->current_work_status,
            $this->contract_status,
            $this->visa_status,
            $this->travel_status,
        ])
            ->filter()
            ->map(fn ($value) => str($value)->lower()->replace([' ', '-'], '_')->toString())
            ->values();

        $blockedStatuses = [
            'resigned',
            'terminated',
            'demobilized',
            'inactive',
            'archived',
            'declined',
            'rejected',
            'visa_declined',
            'visa_rejected',
            'military_zone',
            'restricted_area',
            'blocked',
            'contract_ended',
            'ended',
        ];

        return $values->intersect($blockedStatuses)->isNotEmpty();
    }

    public function resolvedPortalDisabledReason(): ?string
    {
        $values = collect([
            $this->status,
            $this->current_work_status,
            $this->contract_status,
            $this->visa_status,
            $this->travel_status,
        ])
            ->filter()
            ->map(fn ($value) => str($value)->lower()->replace([' ', '-'], '_')->toString())
            ->values();

        foreach ([
            'military_zone',
            'restricted_area',
            'blocked',
            'visa_declined',
            'visa_rejected',
            'resigned',
            'terminated',
            'demobilized',
            'archived',
            'declined',
            'rejected',
            'inactive',
            'contract_ended',
            'ended',
        ] as $reason) {
            if ($values->contains($reason)) {
                return $reason;
            }
        }

        return null;
    }

    public function hasPortalAccess(): bool
    {
        return (bool) $this->portalUser?->hasEmployeePortalAccess();
    }

    public function portalStatusLabel(): string
    {
        return match ($this->portalUser?->portal_status) {
            User::PORTAL_PENDING_PASSWORD_SETUP => 'Pending Password Setup',
            User::PORTAL_ACTIVE => 'Active',
            User::PORTAL_DISABLED => 'Disabled',
            User::PORTAL_BLOCKED => 'Blocked',
            User::PORTAL_ARCHIVED => 'Archived',
            default => 'Not Created',
        };
    }

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

    public function currentFinanceProfile()
    {
        return $this->hasOne(CandidateFinanceProfile::class, 'employment_id')
            ->where('is_current', true)
            ->latest('id');
    }

    public function financeProfiles()
    {
        return $this->hasMany(CandidateFinanceProfile::class, 'employment_id')
            ->latest('effective_from')
            ->latest('id');
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

    public function salarySlips()
    {
        return $this->hasMany(SalarySlip::class, 'employment_id')
            ->orderByDesc('salary_year')
            ->orderByDesc('salary_month')
            ->orderByDesc('id');
    }

    public function financeExpenses()
    {
        return $this->hasMany(FinanceExpense::class, 'employment_id')
            ->orderByDesc('expense_date')
            ->orderByDesc('id');
    }

    public function clientInvoiceLines()
    {
        return $this->hasMany(ClientInvoiceLine::class, 'employment_id')
            ->orderByDesc('id');
    }

    public function hasSalaryConfigured(): bool
    {
        $profile = $this->currentFinanceProfile;

        if (! $profile) {
            return false;
        }

        if ($profile->salary_basis === SalarySlip::BASIS_DAILY_RATE) {
            return filled($profile->daily_rate) && filled($profile->payout_currency);
        }

        if ($profile->salary_basis === SalarySlip::BASIS_MONTHLY) {
            return filled($profile->monthly_salary) && filled($profile->payout_currency);
        }

        return false;
    }

    public function resolvedSalaryBasis(): ?string
    {
        return $this->currentFinanceProfile?->salary_basis;
    }

    public function resolvedDailyRate(): float
    {
        return (float) ($this->currentFinanceProfile?->daily_rate ?? 0);
    }

    public function resolvedMonthlySalary(): float
    {
        return (float) ($this->currentFinanceProfile?->monthly_salary ?? 0);
    }

    public function resolvedSalaryCurrency(): ?string
    {
        return $this->currentFinanceProfile?->payout_currency;
    }

    public function resolvedClientBillingRate(): float
    {
        return (float) ($this->currentFinanceProfile?->resolvedClientBillingRate() ?? 0);
    }

    public function resolvedClientBillingCurrency(): ?string
    {
        return $this->currentFinanceProfile?->resolvedClientBillingCurrency();
    }

    public function totalSalaryCost(): float
    {
        return round((float) $this->salarySlips()
            ->whereIn('status', [
                \App\Models\SalarySlip::STATUS_SENT_TO_BANK,
                \App\Models\SalarySlip::STATUS_PAID,
            ])
            ->sum('net_amount'), 2);
    }

    public function paidSalaryCost(): float
    {
        return round((float) $this->salarySlips()
            ->whereIn('status', [
                \App\Models\SalarySlip::STATUS_SENT_TO_BANK,
                \App\Models\SalarySlip::STATUS_PAID,
            ])
            ->sum('net_amount'), 2);
    }

    public function remainingSalaryCost(): float
    {
        return round(max(0, $this->totalSalaryCost() - $this->paidSalaryCost()), 2);
    }

    public function totalOtherExpenses(): float
    {
        if (! Schema::hasColumn('finance_expenses', 'amount')) {
            return 0;
        }

        return round((float) $this->financeExpenses()->sum('amount'), 2);
    }

    public function totalRevenueGenerated(): float
    {
        return round((float) $this->clientInvoiceLines()->sum('amount'), 2);
    }

    public function revenueByCurrency(): array
    {
        if (! Schema::hasColumn('client_invoice_lines', 'currency') || ! Schema::hasColumn('client_invoice_lines', 'amount')) {
            return [];
        }

        return $this->clientInvoiceLines()
            ->selectRaw('UPPER(COALESCE(currency, "")) as currency, SUM(amount) as total_amount')
            ->groupByRaw('UPPER(COALESCE(currency, ""))')
            ->get()
            ->filter(fn ($row) => filled($row->currency))
            ->mapWithKeys(fn ($row) => [
                strtoupper((string) $row->currency) => round((float) $row->total_amount, 2),
            ])
            ->toArray();
    }

    public function revenueForeignByCurrency(): array
    {
        return collect($this->revenueByCurrency())
            ->reject(fn ($amount, $currency) => strtoupper((string) $currency) === 'LYD')
            ->toArray();
    }

    public function revenueLocalByCurrency(): array
    {
        return collect($this->revenueByCurrency())
            ->filter(fn ($amount, $currency) => strtoupper((string) $currency) === 'LYD')
            ->toArray();
    }

    public function salaryCostByCurrency(): array
    {
        if (! Schema::hasColumn('salary_slips', 'currency') || ! Schema::hasColumn('salary_slips', 'net_amount')) {
            return [];
        }

        return $this->salarySlips()
            ->selectRaw('UPPER(COALESCE(currency, "")) as currency, SUM(net_amount) as total_amount')
            ->groupByRaw('UPPER(COALESCE(currency, ""))')
            ->get()
            ->filter(fn ($row) => filled($row->currency))
            ->mapWithKeys(fn ($row) => [
                strtoupper((string) $row->currency) => round((float) $row->total_amount, 2),
            ])
            ->toArray();
    }

    public function expenseCostByCurrency(): array
    {
        if (! Schema::hasColumn('finance_expenses', 'currency') || ! Schema::hasColumn('finance_expenses', 'amount')) {
            return [];
        }

        return $this->financeExpenses()
            ->selectRaw('UPPER(COALESCE(currency, "")) as currency, SUM(amount) as total_amount')
            ->groupByRaw('UPPER(COALESCE(currency, ""))')
            ->get()
            ->filter(fn ($row) => filled($row->currency))
            ->mapWithKeys(fn ($row) => [
                strtoupper((string) $row->currency) => round((float) $row->total_amount, 2),
            ])
            ->toArray();
    }

    public function totalCostByCurrency(): array
    {
        $currencies = collect(array_merge(
            array_keys($this->salaryCostByCurrency()),
            array_keys($this->expenseCostByCurrency()),
            array_keys($this->revenueByCurrency()),
        ))->unique()->values();

        $salary = $this->salaryCostByCurrency();
        $expenses = $this->expenseCostByCurrency();

        return $currencies
            ->mapWithKeys(fn ($currency) => [
                $currency => round(
                    (float) ($salary[$currency] ?? 0) + (float) ($expenses[$currency] ?? 0),
                    2
                ),
            ])
            ->toArray();
    }

    public function netByCurrency(): array
    {
        $currencies = collect(array_merge(
            array_keys($this->revenueByCurrency()),
            array_keys($this->totalCostByCurrency()),
        ))->unique()->values();

        $revenue = $this->revenueByCurrency();
        $cost = $this->totalCostByCurrency();

        return $currencies
            ->mapWithKeys(fn ($currency) => [
                $currency => round(
                    (float) ($revenue[$currency] ?? 0) - (float) ($cost[$currency] ?? 0),
                    2
                ),
            ])
            ->toArray();
    }

    public function netResult(): float
    {
        return round(collect($this->netByCurrency())->sum(), 2);
    }
}