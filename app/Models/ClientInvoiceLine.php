<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientInvoiceLine extends Model
{
    protected $fillable = [
        'client_invoice_id',
        'employment_id',
        'project_id',
        'salary_slip_id',
        'client_contract_term_id',
        'service_title',
        'position_title',
        'candidate_name',
        'project_name',
        'service_period_start',
        'service_period_end',
        'service_month_label',
        'quantity',
        'unit_rate',
        'amount',
        'foreign_amount',
        'local_amount_foreign_equivalent',
        'local_amount',
        'currency',
        'foreign_currency',
        'local_currency',
        'scope_description',
        'line_notes',
        'sort_order',
    ];

    protected $casts = [
        'service_period_start' => 'date',
        'service_period_end' => 'date',
        'quantity' => 'decimal:2',
        'unit_rate' => 'decimal:2',
        'amount' => 'decimal:2',
        'foreign_amount' => 'decimal:2',
        'local_amount_foreign_equivalent' => 'decimal:2',
        'local_amount' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(ClientInvoice::class, 'client_invoice_id');
    }

    public function employment()
    {
        return $this->belongsTo(Employment::class, 'employment_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function salarySlip()
    {
        return $this->belongsTo(SalarySlip::class, 'salary_slip_id');
    }

    public function clientContractTerm()
    {
        return $this->belongsTo(ClientContractTerm::class, 'client_contract_term_id');
    }
}
