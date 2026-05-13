<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmploymentRotation extends Model
{
    protected $fillable = [
        'employment_id',
        'rotation_label',
        'status',
        'rotation_pattern',
        'travel_status',
        'travel_request_file_path',
        'ticket_file_path',
        'from_date',
        'to_date',
        'mobilization_date',
        'demobilization_date',
        'notes',
        'is_current',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'mobilization_date' => 'date',
        'demobilization_date' => 'date',
        'is_current' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $rotation) {
            if ($rotation->is_current) {
                static::query()
                    ->where('employment_id', $rotation->employment_id)
                    ->where('id', '!=', $rotation->id)
                    ->update(['is_current' => false]);
            }
        });
    }

    public function employment()
    {
        return $this->belongsTo(Employment::class);
    }

    public function financeExpenses()
    {
        return $this->hasMany(FinanceExpense::class, 'employment_rotation_id')
            ->orderByDesc('expense_date')
            ->orderByDesc('id');
    }

    public function salarySlips()
    {
        return $this->hasMany(SalarySlip::class, 'employment_rotation_id')
            ->latest('salary_year')
            ->latest('salary_month')
            ->latest('id');
    }

    public function client()
    {
        return $this->employment?->job?->project?->client();
    }

    public function project()
    {
        return $this->employment?->job?->project();
    }

    public function hasValidPeriod(): bool
    {
        return filled($this->from_date)
            && filled($this->to_date)
            && $this->to_date->gte($this->from_date);
    }

    public function totalExpenseByCurrency(string $currency): float
    {
        return (float) $this->financeExpenses()
            ->active()
            ->where('currency', strtoupper($currency))
            ->sum('amount');
    }

    public function totalSalaryCostByCurrency(string $currency): float
    {
        return (float) $this->salarySlips()
            ->where('currency', strtoupper($currency))
            ->sum('net_amount');
    }

    public function totalCostByCurrency(string $currency): float
    {
        return round(
            $this->totalExpenseByCurrency($currency) + $this->totalSalaryCostByCurrency($currency),
            2
        );
    }

    public function revenueByCurrency(string $currency): float
    {
        return 0.0;
    }

    public function netByCurrency(string $currency): float
    {
        return round($this->revenueByCurrency($currency) - $this->totalCostByCurrency($currency), 2);
    }

    public function financialSnapshot(): array
    {
        $currencies = ['USD', 'EUR', 'GBP', 'LYD'];
        $snapshot = [];

        foreach ($currencies as $currency) {
            $snapshot[$currency] = [
                'salary_cost' => $this->totalSalaryCostByCurrency($currency),
                'rotation_expenses' => $this->totalExpenseByCurrency($currency),
                'total_cost' => $this->totalCostByCurrency($currency),
                'revenue' => $this->revenueByCurrency($currency),
                'net' => $this->netByCurrency($currency),
            ];
        }

        return $snapshot;
    }
}