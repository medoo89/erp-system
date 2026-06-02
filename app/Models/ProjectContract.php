<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectContract extends Model
{
    protected $fillable = [
        'project_id',
        'client_id',
        'contract_no',
        'title',
        'version',
        'type',
        'amendment_type',
        'contract_value',
        'currency',
        'tax_paid',
        'tax_currency',
        'start_date',
        'end_date',
        'status',
        'is_active',
        'file_path',
        'notes',
    ];

    protected $casts = [
        'contract_value' => 'decimal:2',
        'tax_paid' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ProjectContractDocument::class);
    }


    public function financeExpenses(): HasMany
    {
        return $this->hasMany(FinanceExpense::class, 'project_contract_id');
    }


    public function clientInvoices(): HasMany
    {
        return $this->hasMany(ClientInvoice::class, 'project_contract_id');
    }

    public function getClientInvoicedAmountAttribute(): float
    {
        return (float) $this->clientInvoices()
            ->get()
            ->sum(function (ClientInvoice $invoice): float {
                return $invoice->resolveInvoiceAmountForContractAllocation();
            });
    }

    public function getContractRemainingByInvoicesAttribute(): float
    {
        $contractValue = (float) ($this->contract_value ?? 0);

        return max($contractValue - $this->client_invoiced_amount, 0);
    }

    public function getContractConsumedPercentAttribute(): float
    {
        $contractValue = (float) ($this->contract_value ?? 0);

        if ($contractValue <= 0) {
            return 0.0;
        }

        return round(($this->client_invoiced_amount / $contractValue) * 100, 4);
    }

    public function getCompanyExpensesAmountAttribute(): float
    {
        return (float) $this->financeExpenses()->sum('amount');
    }

    public function getAllocatedTaxAmountAttribute(): float
    {
        return (float) $this->clientInvoices()->sum('allocated_contract_tax_amount');
    }

    public function getRemainingTaxPoolAttribute(): float
    {
        $taxPaid = (float) ($this->tax_paid ?? 0);

        return max($taxPaid - $this->allocated_tax_amount, 0);
    }

}
