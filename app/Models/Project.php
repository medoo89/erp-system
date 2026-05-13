<?php

namespace App\Models;

use App\Services\CodeGeneratorService;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'client_id',
        'default_invoice_profile_id',
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

    public function defaultInvoiceProfile()
    {
        return $this->belongsTo(InvoiceProfile::class, 'default_invoice_profile_id');
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function financeProfiles()
    {
        return $this->hasMany(CandidateFinanceProfile::class, 'project_id')
            ->latest('id');
    }

    public function financeExpenses()
    {
        return $this->hasMany(FinanceExpense::class, 'project_id')
            ->latest('expense_date')
            ->latest('id');
    }

    public function paymentStructures()
    {
        return $this->hasMany(ClientPaymentStructure::class, 'project_id')
            ->latest('effective_from')
            ->latest('id');
    }

    public function salaryTermsHistory()
    {
        return $this->hasMany(SalaryTermsHistory::class, 'project_id')
            ->latest('effective_from')
            ->latest('id');
    }

    public function contractTerms()
    {
        return $this->hasMany(ClientContractTerm::class, 'project_id')
            ->latest('effective_from')
            ->latest('id');
    }

    public function salarySlips()
    {
        return $this->hasMany(SalarySlip::class, 'project_id')
            ->orderByDesc('salary_year')
            ->orderByDesc('salary_month')
            ->orderByDesc('id');
    }

    public function clientInvoiceLines()
    {
        return $this->hasMany(ClientInvoiceLine::class, 'project_id')
            ->orderByDesc('id');
    }

    public function clientInvoices()
    {
        return $this->hasMany(ClientInvoice::class, 'project_id')
            ->orderByDesc('invoice_date')
            ->orderByDesc('id');
    }

    public function totalRevenueGenerated(): float
    {
        return round((float) $this->clientInvoiceLines()->sum('amount'), 2);
    }

    public function revenueForeignByCurrency(): array
    {
        return $this->clientInvoiceLines()
            ->selectRaw('foreign_currency as currency, SUM(foreign_amount) as total_amount')
            ->whereNotNull('foreign_currency')
            ->groupBy('foreign_currency')
            ->pluck('total_amount', 'currency')
            ->map(fn ($value) => round((float) $value, 2))
            ->toArray();
    }

    public function revenueLocalByCurrency(): array
    {
        return $this->clientInvoiceLines()
            ->selectRaw('local_currency as currency, SUM(local_amount) as total_amount')
            ->whereNotNull('local_currency')
            ->groupBy('local_currency')
            ->pluck('total_amount', 'currency')
            ->map(fn ($value) => round((float) $value, 2))
            ->toArray();
    }

    public function totalSalaryCost(): float
    {
        return round((float) $this->salarySlips()->sum('net_amount'), 2);
    }

    public function paidSalaryCost(): float
    {
        return round((float) $this->salarySlips()
            ->where('status', SalarySlip::STATUS_PAID)
            ->sum('net_amount'), 2);
    }

    public function remainingSalaryCost(): float
    {
        return round(max(0, $this->totalSalaryCost() - $this->paidSalaryCost()), 2);
    }

    public function totalOtherExpenses(): float
    {
        return round((float) $this->financeExpenses()
            ->whereNotIn('status', ['cancelled'])
            ->sum('amount'), 2);
    }

    public function otherExpensesByCurrency(): array
    {
        return $this->financeExpenses()
            ->whereNotIn('status', ['cancelled'])
            ->selectRaw('currency, SUM(amount) as total_amount')
            ->groupBy('currency')
            ->pluck('total_amount', 'currency')
            ->map(fn ($value) => round((float) $value, 2))
            ->toArray();
    }

    public function totalPaidInvoices(): float
    {
        return round((float) $this->clientInvoices()
            ->where('status', ClientInvoice::STATUS_PAID)
            ->sum('total_amount'), 2);
    }

    public function totalDraftAndUnpaidInvoices(): float
    {
        return round((float) $this->clientInvoices()
            ->whereNotIn('status', [ClientInvoice::STATUS_PAID, 'cancelled'])
            ->sum('total_amount'), 2);
    }

    public function netResult(): float
    {
        return round(
            $this->totalRevenueGenerated()
            - $this->totalSalaryCost()
            - $this->totalOtherExpenses(),
            2
        );
    }
}