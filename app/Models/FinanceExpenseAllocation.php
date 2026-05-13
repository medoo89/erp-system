<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceExpenseAllocation extends Model
{
    protected $fillable = [
        'finance_expense_id',
        'client_id',
        'project_id',
        'employment_id',
        'employment_rotation_id',
        'allocation_type',
        'allocated_amount',
        'allocation_percentage',
        'notes',
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
        'allocation_percentage' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::created(function (self $allocation) {
            $allocation->syncParentAllocationStatus();
        });

        static::updated(function (self $allocation) {
            $allocation->syncParentAllocationStatus();
        });

        static::deleted(function (self $allocation) {
            $allocation->syncParentAllocationStatus();
        });
    }

    public function financeExpense(): BelongsTo
    {
        return $this->belongsTo(FinanceExpense::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function employment(): BelongsTo
    {
        return $this->belongsTo(Employment::class);
    }

    public function rotation(): BelongsTo
    {
        return $this->belongsTo(EmploymentRotation::class, 'employment_rotation_id');
    }

    protected function syncParentAllocationStatus(): void
    {
        $expense = FinanceExpense::query()->find($this->finance_expense_id);

        if (! $expense) {
            return;
        }

        $allocatedTotal = (float) static::query()
            ->where('finance_expense_id', $expense->id)
            ->sum('allocated_amount');

        $expenseAmount = (float) ($expense->amount ?? 0);

        if ($allocatedTotal <= 0) {
            $expense->allocation_status = FinanceExpense::ALLOCATION_UNALLOCATED;
        } elseif ($allocatedTotal < $expenseAmount) {
            $expense->allocation_status = FinanceExpense::ALLOCATION_PARTIAL;
        } else {
            $expense->allocation_status = FinanceExpense::ALLOCATION_ALLOCATED;
        }

        $expense->saveQuietly();
    }
}
