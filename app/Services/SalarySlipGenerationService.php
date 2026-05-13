<?php

namespace App\Services;

use App\Models\Employment;

use App\Models\EmploymentRotation;

use App\Models\Project;

use App\Models\SalarySlip;

use App\Models\SalarySlipDay;

use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;

class SalarySlipGenerationService

{

    public function generateForEmploymentRotation(

        EmploymentRotation $rotation,

        bool $replaceExisting = true,

        ?int $generatedBy = null

    ): array {

        $rotation->loadMissing([

            'employment.job.project.client',

            'employment.currentFinanceProfile',

            'employment.preEmployment',

        ]);

        $employment = $rotation->employment;

        if (! $employment) {

            throw new \RuntimeException('Rotation is not linked to an employment record.');

        }

        $financeProfile = $employment->currentFinanceProfile;

        if (! $financeProfile) {

            throw new \RuntimeException('Current Finance Profile is missing.');

        }

        if (! $employment->hasSalaryConfigured()) {

            throw new \RuntimeException('Employment salary is not configured in Current Finance Profile.');

        }

        $salaryBasis = $employment->resolvedSalaryBasis();

        $dailyRate = (float) $employment->resolvedDailyRate();

        $monthlySalary = (float) $employment->resolvedMonthlySalary();

        $salaryCurrency = $employment->resolvedSalaryCurrency();

        if (blank($salaryBasis)) {

            throw new \RuntimeException('Salary basis is missing in Current Finance Profile.');

        }

        if (blank($salaryCurrency)) {

            throw new \RuntimeException('Payout currency is missing in Current Finance Profile.');

        }

        if ($salaryBasis === SalarySlip::BASIS_DAILY_RATE && $dailyRate <= 0) {

            throw new \RuntimeException('Daily rate is missing in Current Finance Profile.');

        }

        if ($salaryBasis === SalarySlip::BASIS_MONTHLY && $monthlySalary <= 0) {

            throw new \RuntimeException('Monthly salary is missing in Current Finance Profile.');

        }

        if (blank($rotation->from_date) || blank($rotation->to_date)) {

            throw new \RuntimeException('Rotation start date or end date is missing.');

        }

        $rotationStart = Carbon::parse($rotation->from_date)->startOfDay();

        $rotationEnd = Carbon::parse($rotation->to_date)->startOfDay();

        if ($rotationEnd->lt($rotationStart)) {

            throw new \RuntimeException('Rotation end date cannot be before rotation start date.');

        }

        $periods = $this->splitDateRangeByMonth($rotationStart, $rotationEnd);

        $generated = [];

        DB::transaction(function () use (

            $periods,

            $rotation,

            $employment,

            $financeProfile,

            $salaryBasis,

            $dailyRate,

            $monthlySalary,

            $salaryCurrency,

            $replaceExisting,

            $generatedBy,

            &$generated

        ) {

            foreach ($periods as $period) {

                $periodStart = $period['period_start']->copy();

                $periodEnd = $period['period_end']->copy();

                $attributes = [

                    'employment_id' => $employment->id,

                    'salary_year' => (int) $periodStart->format('Y'),

                    'salary_month' => (int) $periodStart->format('m'),

                    'period_start' => $periodStart->toDateString(),

                    'period_end' => $periodEnd->toDateString(),

                ];

                $values = [

                    'job_application_id' => $employment->preEmployment?->job_application_id,

                    'client_id' => $employment->job?->project?->client?->id,

                    'project_id' => $employment->job?->project?->id,

                    'employment_rotation_id' => $rotation->id,

                    'candidate_finance_profile_id' => $financeProfile->id,

                    'days_worked' => 0,

                    'salary_basis' => $salaryBasis,

                    'daily_rate' => $salaryBasis === SalarySlip::BASIS_DAILY_RATE ? $dailyRate : null,

                    'monthly_salary' => $salaryBasis === SalarySlip::BASIS_MONTHLY ? $monthlySalary : null,

                    'base_amount' => 0,

                    'adjustments_amount' => 0,

                    'deductions_amount' => 0,

                    'net_amount' => 0,

                    'currency' => $salaryCurrency,

                    'status' => SalarySlip::STATUS_DRAFT,

                    'notes' => 'Generated automatically from rotation and Current Finance Profile.',

                    'generated_by' => $generatedBy ?? Auth::id(),

                    'generated_at' => now(),

                ];

                if ($replaceExisting) {

                    $salarySlip = SalarySlip::updateOrCreate($attributes, $values);

                } else {

                    $existing = SalarySlip::query()

                        ->where($attributes)

                        ->first();

                    if ($existing) {

                        $generated[] = $existing;

                        continue;

                    }

                    $salarySlip = SalarySlip::create(array_merge($attributes, $values));

                }

                $this->syncSalarySlipDays($salarySlip, $periodStart, $periodEnd);

                $this->recalculateSalarySlip($salarySlip);

                $generated[] = $salarySlip->fresh(['days']);

            }

        });

        return $generated;

    }

    public function generateForEmploymentMonth(

        Employment $employment,

        int $year,

        int $month,

        bool $replaceExisting = true,

        ?int $generatedBy = null

    ): ?SalarySlip {

        $employment->loadMissing([

            'rotations',

            'job.project.client',

            'currentFinanceProfile',

            'preEmployment',

        ]);

        $financeProfile = $employment->currentFinanceProfile;

        if (! $financeProfile) {

            throw new \RuntimeException('Current Finance Profile is missing.');

        }

        if (! $employment->hasSalaryConfigured()) {

            throw new \RuntimeException('Employment salary is not configured in Current Finance Profile.');

        }

        $salaryBasis = $employment->resolvedSalaryBasis();

        $dailyRate = (float) $employment->resolvedDailyRate();

        $monthlySalary = (float) $employment->resolvedMonthlySalary();

        $salaryCurrency = $employment->resolvedSalaryCurrency();

        if (blank($salaryBasis)) {

            throw new \RuntimeException('Salary basis is missing in Current Finance Profile.');

        }

        if (blank($salaryCurrency)) {

            throw new \RuntimeException('Payout currency is missing in Current Finance Profile.');

        }

        if ($salaryBasis === SalarySlip::BASIS_DAILY_RATE && $dailyRate <= 0) {

            throw new \RuntimeException('Daily rate is missing in Current Finance Profile.');

        }

        if ($salaryBasis === SalarySlip::BASIS_MONTHLY && $monthlySalary <= 0) {

            throw new \RuntimeException('Monthly salary is missing in Current Finance Profile.');

        }

        $monthStart = Carbon::create($year, $month, 1)->startOfDay();

        $monthEnd = $monthStart->copy()->endOfMonth()->startOfDay();

        $matchedRotation = null;

        $effectiveStart = null;

        $effectiveEnd = null;

        foreach ($employment->rotations as $rotation) {

            if (blank($rotation->from_date) || blank($rotation->to_date)) {

                continue;

            }

            $rotationStart = Carbon::parse($rotation->from_date)->startOfDay();

            $rotationEnd = Carbon::parse($rotation->to_date)->startOfDay();

            if ($rotationEnd->lt($monthStart) || $rotationStart->gt($monthEnd)) {

                continue;

            }

            $intersectionStart = $rotationStart->gt($monthStart) ? $rotationStart->copy() : $monthStart->copy();

            $intersectionEnd = $rotationEnd->lt($monthEnd) ? $rotationEnd->copy() : $monthEnd->copy();

            if ($intersectionEnd->lt($intersectionStart)) {

                continue;

            }

            if (! $matchedRotation) {

                $matchedRotation = $rotation;

                $effectiveStart = $intersectionStart;

                $effectiveEnd = $intersectionEnd;

            } else {

                if ($intersectionStart->lt($effectiveStart)) {

                    $effectiveStart = $intersectionStart;

                }

                if ($intersectionEnd->gt($effectiveEnd)) {

                    $effectiveEnd = $intersectionEnd;

                }

            }

        }

        if (! $matchedRotation || ! $effectiveStart || ! $effectiveEnd) {

            return null;

        }

        return DB::transaction(function () use (

            $employment,

            $matchedRotation,

            $financeProfile,

            $salaryBasis,

            $dailyRate,

            $monthlySalary,

            $salaryCurrency,

            $effectiveStart,

            $effectiveEnd,

            $year,

            $month,

            $replaceExisting,

            $generatedBy

        ) {

            $attributes = [

                'employment_id' => $employment->id,

                'salary_year' => $year,

                'salary_month' => $month,

                'period_start' => $effectiveStart->toDateString(),

                'period_end' => $effectiveEnd->toDateString(),

            ];

            $values = [

                'job_application_id' => $employment->preEmployment?->job_application_id,

                'client_id' => $employment->job?->project?->client?->id,

                'project_id' => $employment->job?->project?->id,

                'employment_rotation_id' => $matchedRotation->id,

                'candidate_finance_profile_id' => $financeProfile->id,

                'days_worked' => 0,

                'salary_basis' => $salaryBasis,

                'daily_rate' => $salaryBasis === SalarySlip::BASIS_DAILY_RATE ? $dailyRate : null,

                'monthly_salary' => $salaryBasis === SalarySlip::BASIS_MONTHLY ? $monthlySalary : null,

                'base_amount' => 0,

                'adjustments_amount' => 0,

                'deductions_amount' => 0,

                'net_amount' => 0,

                'currency' => $salaryCurrency,

                'status' => SalarySlip::STATUS_DRAFT,

                'notes' => 'Generated automatically for selected month using Current Finance Profile.',

                'generated_by' => $generatedBy ?? Auth::id(),

                'generated_at' => now(),

            ];

            if ($replaceExisting) {

                $salarySlip = SalarySlip::updateOrCreate($attributes, $values);

            } else {

                $existing = SalarySlip::query()

                    ->where($attributes)

                    ->first();

                if ($existing) {

                    return $existing;

                }

                $salarySlip = SalarySlip::create(array_merge($attributes, $values));

            }

            $this->syncSalarySlipDays($salarySlip, $effectiveStart, $effectiveEnd);

            $this->recalculateSalarySlip($salarySlip);

            return $salarySlip->fresh(['days']);

        });

    }

    public function generateForProjectMonth(

        Project $project,

        int $year,

        int $month,

        array $employmentIds = [],

        bool $replaceExisting = true,

        ?int $generatedBy = null

    ): array {

        $jobIds = $project->jobs()->pluck('id')->filter()->values()->all();

        $query = Employment::query()

            ->with([

                'rotations',

                'job.project.client',

                'currentFinanceProfile',

                'preEmployment',

            ])

            ->where(function ($query) use ($project, $jobIds) {

                if (! empty($jobIds)) {

                    $query->whereIn('job_id', $jobIds);

                }

                $query->orWhere('project_name', $project->name);

            })

            ->whereNotNull('employee_name');

        if (! empty($employmentIds)) {

            $query->whereIn('id', $employmentIds);

        }

        $employments = $query

            ->orderBy('employee_name')

            ->get()

            ->unique('id')

            ->values();

        if ($employments->isEmpty()) {

            throw new \RuntimeException('No employments were found for the selected project.');

        }

        $generated = [];

        foreach ($employments as $employment) {

            try {

                $salarySlip = $this->generateForEmploymentMonth(

                    $employment,

                    $year,

                    $month,

                    $replaceExisting,

                    $generatedBy

                );

                if ($salarySlip) {

                    $generated[$salarySlip->id] = $salarySlip;

                }

            } catch (\Throwable $e) {

                throw new \RuntimeException(

                    'Could not generate salary slip for ' . ($employment->employee_name ?: 'Unknown Employee') . ': ' . $e->getMessage()

                );

            }

        }

        return array_values($generated);

    }

    public function generateAllForEmployment(

        Employment $employment,

        bool $replaceExisting = true,

        ?int $generatedBy = null

    ): array {

        $employment->loadMissing('rotations', 'currentFinanceProfile');

        $financeProfile = $employment->currentFinanceProfile;

        if (! $financeProfile) {

            throw new \RuntimeException('Current Finance Profile is missing.');

        }

        if (! $employment->hasSalaryConfigured()) {

            throw new \RuntimeException('Employment salary is not configured in Current Finance Profile.');

        }

        $generated = [];

        foreach ($employment->rotations as $rotation) {

            if (blank($rotation->from_date) || blank($rotation->to_date)) {

                continue;

            }

            $slips = $this->generateForEmploymentRotation(

                $rotation,

                $replaceExisting,

                $generatedBy

            );

            foreach ($slips as $slip) {

                $generated[$slip->id] = $slip;

            }

        }

        return array_values($generated);

    }

    public function recalculateSalarySlip(SalarySlip $salarySlip): SalarySlip

    {

        $salarySlip->loadMissing('days', 'employment.currentFinanceProfile');

        $salaryBasis = $salarySlip->salary_basis ?: $salarySlip->employment?->resolvedSalaryBasis();

        if (blank($salaryBasis)) {

            throw new \RuntimeException('Salary basis is missing.');

        }

        $paidDays = $salarySlip->days

            ->where('is_paid_day', true)

            ->count();

        $adjustments = (float) ($salarySlip->adjustments_amount ?? 0);

        $deductions = (float) ($salarySlip->deductions_amount ?? 0);

        if ($salaryBasis === SalarySlip::BASIS_DAILY_RATE) {

            $dailyRate = (float) (

                $salarySlip->daily_rate

                ?? $salarySlip->employment?->resolvedDailyRate()

                ?? 0

            );

            if ($dailyRate <= 0) {

                throw new \RuntimeException('Daily rate is missing for salary slip recalculation.');

            }

            $baseAmount = round($dailyRate * $paidDays, 2);

        } elseif ($salaryBasis === SalarySlip::BASIS_MONTHLY) {

            $monthlySalary = (float) (

                $salarySlip->monthly_salary

                ?? $salarySlip->employment?->resolvedMonthlySalary()

                ?? 0

            );

            if ($monthlySalary <= 0) {

                throw new \RuntimeException('Monthly salary is missing for salary slip recalculation.');

            }

            $scheduledDays = max(1, $salarySlip->days->count());

            $baseAmount = round(($monthlySalary / $scheduledDays) * $paidDays, 2);

        } else {

            throw new \RuntimeException('Unsupported salary basis for salary slip recalculation.');

        }

        $netAmount = round($baseAmount + $adjustments - $deductions, 2);

        $salarySlip->update([

            'days_worked' => $paidDays,

            'salary_basis' => $salaryBasis,

            'base_amount' => $baseAmount,

            'net_amount' => $netAmount,

        ]);

        return $salarySlip->fresh(['days']);

    }

    protected function syncSalarySlipDays(SalarySlip $salarySlip, Carbon $start, Carbon $end): void

    {

        $existingByDate = $salarySlip->days()

            ->get()

            ->keyBy(fn ($day) => $day->work_date->toDateString());

        $cursor = $start->copy();

        while ($cursor->lte($end)) {

            $dateKey = $cursor->toDateString();

            $existing = $existingByDate->get($dateKey);

            if ($existing) {

                $existing->update([

                    'day_name' => $cursor->format('l'),

                ]);

            } else {

                $salarySlip->days()->create([

                    'work_date' => $dateKey,

                    'day_name' => $cursor->format('l'),

                    'attendance_status' => SalarySlipDay::STATUS_PRESENT,

                    'is_paid_day' => true,

                    'notes' => null,

                ]);

            }

            $cursor->addDay();

        }

        $salarySlip->days()

            ->whereDate('work_date', '<', $start->toDateString())

            ->orWhereDate('work_date', '>', $end->toDateString())

            ->delete();

    }

    protected function splitDateRangeByMonth(Carbon $start, Carbon $end): array

    {

        $periods = [];

        $cursor = $start->copy();

        while ($cursor->lte($end)) {

            $monthStart = $cursor->copy()->startOfMonth();

            $monthEnd = $cursor->copy()->endOfMonth()->startOfDay();

            $periodStart = $cursor->copy()->gt($monthStart)

                ? $cursor->copy()

                : $monthStart->copy();

            $periodEnd = $end->copy()->lt($monthEnd)

                ? $end->copy()

                : $monthEnd->copy();

            $periods[] = [

                'period_start' => $periodStart,

                'period_end' => $periodEnd,

                'days_worked' => $periodStart->diffInDays($periodEnd) + 1,

            ];

            $cursor = $periodEnd->copy()->addDay()->startOfDay();

        }

        return $periods;

    }

}