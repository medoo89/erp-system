<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalarySlip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SalarySlipAttendanceController extends Controller
{
    public function edit(SalarySlip $salarySlip)
    {
        $salarySlip->load(['days', 'employment']);

        return view('admin.salary-slips.attendance', [
            'salarySlip' => $salarySlip,
        ]);
    }

    public function update(Request $request, SalarySlip $salarySlip)
    {
        $salarySlip->load('days');

        $statuses = $request->input('status', []);
        $notes = $request->input('notes', []);
        $paid = $request->input('paid', []);

        foreach ($salarySlip->days as $day) {
            $dayId = (string) $day->id;

            $status = $statuses[$dayId] ?? 'present';

            if (in_array($status, ['absent', 'unpaid_leave'], true)) {
                $isPaid = false;
            } else {
                $isPaid = array_key_exists($dayId, $paid);
            }

            $payload = [];

            if (Schema::hasColumn($day->getTable(), 'status')) {
                $payload['status'] = $status;
            }

            if (Schema::hasColumn($day->getTable(), 'is_paid')) {
                $payload['is_paid'] = $isPaid;
            }

            if (Schema::hasColumn($day->getTable(), 'paid')) {
                $payload['paid'] = $isPaid;
            }

            if (Schema::hasColumn($day->getTable(), 'notes')) {
                $payload['notes'] = $notes[$dayId] ?? null;
            }

            $day->forceFill($payload)->save();
        }

        $this->recalculate($salarySlip);

        return redirect()
            ->route('filament.admin.resources.salary-slips.view', ['record' => $salarySlip])
            ->with('success', 'Attendance saved and salary slip recalculated.');
    }

    protected function recalculate(SalarySlip $salarySlip): void
    {
        $salarySlip->refresh();
        $salarySlip->load('days');

        $paidDays = $salarySlip->days->filter(function ($day): bool {
            $status = $day->status ?: 'present';

            if (in_array($status, ['absent', 'unpaid_leave'], true)) {
                return false;
            }

            if (Schema::hasColumn($day->getTable(), 'is_paid')) {
                return (bool) $day->is_paid;
            }

            if (Schema::hasColumn($day->getTable(), 'paid')) {
                return (bool) $day->paid;
            }

            return true;
        })->count();

        $dailyRate = (float) ($salarySlip->daily_rate ?? 0);
        $monthlySalary = (float) ($salarySlip->monthly_salary ?? 0);
        $adjustments = (float) ($salarySlip->adjustments_amount ?? 0);
        $deductions = (float) ($salarySlip->deductions_amount ?? 0);

        if (($salarySlip->salary_basis ?? null) === SalarySlip::BASIS_MONTHLY && $monthlySalary > 0) {
            $baseAmount = round(($monthlySalary / max(1, $salarySlip->days->count())) * $paidDays, 2);
        } else {
            $baseAmount = round($dailyRate * $paidDays, 2);
        }

        $netAmount = round($baseAmount + $adjustments - $deductions, 2);

        $salarySlip->forceFill([
            'days_worked' => $paidDays,
            'base_amount' => $baseAmount,
            'net_amount' => $netAmount,
        ])->save();

        if (method_exists($salarySlip, 'syncTreasuryPosting')) {
            $salarySlip->syncTreasuryPosting();
        }
    }
}
