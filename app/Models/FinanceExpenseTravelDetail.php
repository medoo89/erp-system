<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceExpenseTravelDetail extends Model
{
    protected $fillable = [
        'finance_expense_id',
        'employment_id',
        'traveler_name',
        'trip_type',
        'origin',
        'destination',
        'departure_date',
        'return_date',
        'return_open',
        'split_across_rotations',
        'outbound_rotation_id',
        'inbound_rotation_id',
        'notes',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'return_open' => 'boolean',
        'split_across_rotations' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $detail) {
            $detail->syncSplitAllocations();
        });

        static::deleted(function (self $detail) {
            $expense = $detail->financeExpense;

            if (! $expense) {
                return;
            }

            $expense->allocations()
                ->where('allocation_type', 'rotation')
                ->whereIn('employment_rotation_id', array_filter([
                    $detail->outbound_rotation_id,
                    $detail->inbound_rotation_id,
                ]))
                ->delete();
        });
    }

    public function financeExpense(): BelongsTo
    {
        return $this->belongsTo(FinanceExpense::class);
    }

    public function employment(): BelongsTo
    {
        return $this->belongsTo(Employment::class);
    }

    public function outboundRotation(): BelongsTo
    {
        return $this->belongsTo(EmploymentRotation::class, 'outbound_rotation_id');
    }

    public function inboundRotation(): BelongsTo
    {
        return $this->belongsTo(EmploymentRotation::class, 'inbound_rotation_id');
    }

    public function syncSplitAllocations(): void
    {
        $expense = $this->financeExpense;

        if (! $expense) {
            return;
        }

        if (! $this->split_across_rotations || ! $this->outbound_rotation_id || ! $this->inbound_rotation_id) {
            return;
        }

        $amount = (float) ($expense->amount ?? 0);

        if ($amount <= 0) {
            return;
        }

        $half = round($amount / 2, 2);
        $secondHalf = round($amount - $half, 2);

        $rotationIds = [$this->outbound_rotation_id, $this->inbound_rotation_id];

        $expense->allocations()
            ->where('allocation_type', 'rotation')
            ->whereIn('employment_rotation_id', $rotationIds)
            ->delete();

        $outboundRotation = EmploymentRotation::query()->find($this->outbound_rotation_id);
        $inboundRotation = EmploymentRotation::query()->find($this->inbound_rotation_id);

        $expense->allocations()->create([
            'client_id' => $expense->client_id,
            'project_id' => $expense->project_id,
            'employment_id' => $expense->employment_id,
            'employment_rotation_id' => $this->outbound_rotation_id,
            'allocation_type' => 'rotation',
            'allocated_amount' => $half,
            'allocation_percentage' => 50,
            'notes' => 'Auto-generated from travel split helper (outbound rotation).',
        ]);

        $expense->allocations()->create([
            'client_id' => $expense->client_id,
            'project_id' => $expense->project_id,
            'employment_id' => $expense->employment_id,
            'employment_rotation_id' => $this->inbound_rotation_id,
            'allocation_type' => 'rotation',
            'allocated_amount' => $secondHalf,
            'allocation_percentage' => 50,
            'notes' => 'Auto-generated from travel split helper (inbound rotation).',
        ]);

        $allocatedTotal = (float) $expense->allocations()->sum('allocated_amount');

        if ($allocatedTotal <= 0) {
            $expense->allocation_status = FinanceExpense::ALLOCATION_UNALLOCATED;
        } elseif ($allocatedTotal < (float) $expense->amount) {
            $expense->allocation_status = FinanceExpense::ALLOCATION_PARTIAL;
        } else {
            $expense->allocation_status = FinanceExpense::ALLOCATION_ALLOCATED;
        }

        $expense->saveQuietly();
    }
}
