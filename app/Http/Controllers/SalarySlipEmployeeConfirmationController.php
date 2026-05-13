<?php

namespace App\Http\Controllers;

use App\Models\SalarySlip;
use Illuminate\Http\Request;

class SalarySlipEmployeeConfirmationController extends Controller
{
    public function confirm(Request $request, SalarySlip $salarySlip)
    {
        $salarySlip->refresh();

        if ($salarySlip->status === SalarySlip::STATUS_PAID) {
            return view('salary-slips.confirmation-result', [
                'title' => 'Already Confirmed',
                'message' => 'This salary slip was already marked as paid.',
                'status' => 'info',
                'salarySlip' => $salarySlip,
            ]);
        }

        if ($salarySlip->status !== SalarySlip::STATUS_SENT_TO_BANK) {
            return view('salary-slips.confirmation-result', [
                'title' => 'Confirmation Not Available',
                'message' => 'This salary slip is not waiting for employee payment confirmation.',
                'status' => 'warning',
                'salarySlip' => $salarySlip,
            ]);
        }

        $existingNotes = trim((string) $salarySlip->notes);
        $confirmationNote = 'Employee confirmed salary receipt by email.';

        $salarySlip->update([
            'status' => SalarySlip::STATUS_PAID,
            'payment_method' => SalarySlip::PAYMENT_METHOD_BANK,
            'paid_at' => now(),
            'notes' => $existingNotes === ''
                ? $confirmationNote
                : $existingNotes . "\n\n" . $confirmationNote,
        ]);

        $salarySlip->refresh();

        return view('salary-slips.confirmation-result', [
            'title' => 'Receipt Confirmed',
            'message' => 'Thank you. Your salary receipt has been confirmed successfully.',
            'status' => 'success',
            'salarySlip' => $salarySlip,
        ]);
    }

    public function reportNotReceived(Request $request, SalarySlip $salarySlip)
    {
        $salarySlip->refresh();

        if ($salarySlip->status === SalarySlip::STATUS_BANK_REJECTED) {
            return view('salary-slips.confirmation-result', [
                'title' => 'Already Reported',
                'message' => 'This salary slip was already marked as not received / bank rejected.',
                'status' => 'warning',
                'salarySlip' => $salarySlip,
            ]);
        }

        if ($salarySlip->status === SalarySlip::STATUS_PAID) {
            return view('salary-slips.confirmation-result', [
                'title' => 'Action Not Available',
                'message' => 'This salary slip is already marked as paid, so it can no longer be reported as not received.',
                'status' => 'info',
                'salarySlip' => $salarySlip,
            ]);
        }

        if ($salarySlip->status !== SalarySlip::STATUS_SENT_TO_BANK) {
            return view('salary-slips.confirmation-result', [
                'title' => 'Action Not Available',
                'message' => 'This salary slip is not currently waiting for employee confirmation.',
                'status' => 'warning',
                'salarySlip' => $salarySlip,
            ]);
        }

        $existingNotes = trim((string) $salarySlip->notes);
        $reportNote = 'Employee reported by email that salary was not received.';

        $salarySlip->update([
            'status' => SalarySlip::STATUS_BANK_REJECTED,
            'paid_at' => null,
            'notes' => $existingNotes === ''
                ? $reportNote
                : $existingNotes . "\n\n" . $reportNote,
        ]);

        $salarySlip->refresh();

        return view('salary-slips.confirmation-result', [
            'title' => 'Report Submitted',
            'message' => 'Your report was submitted successfully. The finance team has been informed that the amount was not received.',
            'status' => 'warning',
            'salarySlip' => $salarySlip,
        ]);
    }
}
