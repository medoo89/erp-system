<?php

namespace App\Http\Controllers;

use App\Models\SalarySlip;
use App\Models\SalarySlipDay;

class SalarySlipPrintController extends Controller
{
    public function show(SalarySlip $salarySlip)
    {
        $salarySlip->loadMissing([
            'employment',
            'client',
            'project.client',
            'days',
            'treasuryAccount',
            'generatedBy',
        ]);

        $logoPath = public_path('logo.png');
        $logoDataUri = null;

        if (is_file($logoPath)) {
            $mime = mime_content_type($logoPath) ?: 'image/png';
            $logoDataUri = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        $days = $salarySlip->days ?? collect();

        $statusBreakdown = [
            'present' => (int) $days->where('attendance_status', SalarySlipDay::STATUS_PRESENT)->count(),
            'absent' => (int) $days->where('attendance_status', SalarySlipDay::STATUS_ABSENT)->count(),
            'sick' => (int) $days->where('attendance_status', SalarySlipDay::STATUS_SICK)->count(),
            'leave' => (int) $days->where('attendance_status', SalarySlipDay::STATUS_LEAVE)->count(),
            'unpaid_leave' => (int) $days->where('attendance_status', SalarySlipDay::STATUS_UNPAID_LEAVE)->count(),
            'holiday' => (int) $days->where('attendance_status', SalarySlipDay::STATUS_HOLIDAY)->count(),
            'travel' => (int) $days->where('attendance_status', SalarySlipDay::STATUS_TRAVEL)->count(),
            'other' => (int) $days->where('attendance_status', SalarySlipDay::STATUS_OTHER)->count(),
        ];

        $totalDaysInSlip = (float) ($days->count() ?: $salarySlip->days_worked ?? 0);
        $workedDays = $totalDaysInSlip;
        $paidDays = (float) $days->where('is_paid_day', true)->count();
        $absentDays = (int) $statusBreakdown['absent'];

        $currency = (string) ($salarySlip->currency ?: '-');
        $netAmount = (float) ($salarySlip->net_amount ?? 0);

        return view('Print.salary-slip', [
            'salarySlip' => $salarySlip,
            'logoDataUri' => $logoDataUri,

            'employeeName' => $salarySlip->employment?->employee_name ?? '-',
            'employeeCode' => $salarySlip->employment?->employee_code ?? '-',
            'positionTitle' => $salarySlip->employment?->position_title ?? '-',

            'clientName' => $salarySlip->client?->name ?? $salarySlip->project?->client?->name ?? '-',
            'projectName' => $salarySlip->project?->name ?? '-',

            'periodLabel' => ($salarySlip->salary_year && $salarySlip->salary_month)
                ? sprintf('%04d-%02d', (int) $salarySlip->salary_year, (int) $salarySlip->salary_month)
                : '-',

            'currency' => $currency,
            'paymentMethodLabel' => $salarySlip->payment_method
                ? (SalarySlip::paymentMethodLabels()[$salarySlip->payment_method] ?? $salarySlip->payment_method)
                : '-',

            'baseAmount' => (float) ($salarySlip->base_amount ?? 0),
            'adjustmentsAmount' => (float) ($salarySlip->adjustments_amount ?? 0),
            'deductionsAmount' => (float) ($salarySlip->deductions_amount ?? 0),
            'netAmount' => $netAmount,

            'workedDays' => $workedDays,
            'paidDays' => $paidDays,
            'totalDaysInSlip' => $totalDaysInSlip,
            'absentDays' => $absentDays,

            'treasuryAccountName' => $salarySlip->treasuryAccount?->account_name ?? '-',
            'notes' => $salarySlip->notes ?? '-',

            'amountInWords' => number_format($netAmount, 2) . ' ' . $currency,
            'statusBreakdown' => $statusBreakdown,
        ]);
    }

    public function print(\App\Models\SalarySlip $salarySlip)
    {
        if (method_exists($this, 'show')) {
            return $this->show($salarySlip);
        }

        if (method_exists($this, '__invoke')) {
            return $this->__invoke($salarySlip);
        }

        return view('Print.salary-slip', [
            'salarySlip' => $salarySlip,
            'record' => $salarySlip,
        ]);
    }

}
