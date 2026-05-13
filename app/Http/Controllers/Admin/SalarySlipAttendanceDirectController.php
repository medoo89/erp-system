<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalarySlip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalarySlipAttendanceDirectController extends Controller
{
    public function edit(SalarySlip $salarySlip)
    {
        $days = DB::table('salary_slip_days')
            ->where('salary_slip_id', $salarySlip->id)
            ->orderBy('work_date')
            ->orderBy('id')
            ->get();

        return view('admin.salary-slips.attendance-direct', [
            'salarySlip' => $salarySlip,
            'days' => $days,
        ]);
    }

    public function update(Request $request, SalarySlip $salarySlip)
    {
        $statuses = $request->input('status', []);
        $notes = $request->input('notes', []);
        $paid = $request->input('paid', []);

        $updatedRows = 0;

        $days = DB::table('salary_slip_days')
            ->where('salary_slip_id', $salarySlip->id)
            ->get();

        foreach ($days as $day) {
            $dayId = (string) $day->id;

            $status = $statuses[$dayId] ?? 'present';

            if (in_array($status, ['absent', 'unpaid_leave'], true)) {
                $isPaid = false;
            } else {
                $isPaid = array_key_exists($dayId, $paid);
            }

            $updatedRows += DB::table('salary_slip_days')
                ->where('id', $day->id)
                ->where('salary_slip_id', $salarySlip->id)
                ->update([
                    'attendance_status' => $status,
                    'is_paid_day' => $isPaid ? 1 : 0,
                    'notes' => $notes[$dayId] ?? null,
                    'updated_at' => now(),
                ]);
        }

        $result = $this->recalculateSalarySlip($salarySlip);

        return redirect()
            ->route('admin.salary-slips.attendance.direct.edit', ['salarySlip' => $salarySlip->id])
            ->with('success', 'Attendance saved successfully.')
            ->with('debug', [
                'updated_rows' => $updatedRows,
                'paid_days' => $result['paid_days'],
                'not_paid_days' => $result['not_paid_days'],
                'base_amount' => $result['base_amount'],
                'net_amount' => $result['net_amount'],
            ]);
    }

    protected function recalculateSalarySlip(SalarySlip $salarySlip): array
    {
        $days = DB::table('salary_slip_days')
            ->where('salary_slip_id', $salarySlip->id)
            ->get();

        $paidDays = 0;
        $notPaidDays = 0;

        foreach ($days as $day) {
            $status = $day->attendance_status ?: 'present';

            if (in_array($status, ['absent', 'unpaid_leave'], true)) {
                $notPaidDays++;
                continue;
            }

            if ((bool) $day->is_paid_day) {
                $paidDays++;
            } else {
                $notPaidDays++;
            }
        }

        $dailyRate = (float) ($salarySlip->daily_rate ?? 0);
        $monthlySalary = (float) ($salarySlip->monthly_salary ?? 0);
        $adjustments = (float) ($salarySlip->adjustments_amount ?? 0);
        $deductions = (float) ($salarySlip->deductions_amount ?? 0);

        if (($salarySlip->salary_basis ?? null) === SalarySlip::BASIS_MONTHLY && $monthlySalary > 0) {
            $baseAmount = round(($monthlySalary / max(1, $days->count())) * $paidDays, 2);
        } else {
            $baseAmount = round($dailyRate * $paidDays, 2);
        }

        $netAmount = round($baseAmount + $adjustments - $deductions, 2);

        DB::table('salary_slips')
            ->where('id', $salarySlip->id)
            ->update([
                'days_worked' => $paidDays,
                'base_amount' => $baseAmount,
                'net_amount' => $netAmount,
                'updated_at' => now(),
            ]);

        $salarySlip->refresh();

        if (method_exists($salarySlip, 'syncTreasuryPosting')) {
            $salarySlip->syncTreasuryPosting();
        }

        return [
            'paid_days' => $paidDays,
            'not_paid_days' => $notPaidDays,
            'base_amount' => $baseAmount,
            'net_amount' => $netAmount,
        ];
    }
}
