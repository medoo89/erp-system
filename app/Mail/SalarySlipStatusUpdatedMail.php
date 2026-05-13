<?php

namespace App\Mail;

use App\Models\SalarySlip;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class SalarySlipStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public SalarySlip $salarySlip;
    public string $oldStatusLabel;
    public string $newStatusLabel;

    public function __construct(SalarySlip $salarySlip, string $oldStatusLabel, string $newStatusLabel)
    {
        $this->salarySlip = $salarySlip;
        $this->oldStatusLabel = $oldStatusLabel;
        $this->newStatusLabel = $newStatusLabel;
    }

    public function envelope(): Envelope
    {
        $employeeName = $this->salarySlip->employment?->employee_name ?: 'Employee';
        $period = ($this->salarySlip->salary_year ?: '-') . '-' . str_pad((string) ($this->salarySlip->salary_month ?: 0), 2, '0', STR_PAD_LEFT);

        return new Envelope(
            from: new Address(
                env('FINANCE_MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS', 'hello@example.com')),
                env('FINANCE_MAIL_FROM_NAME', 'Sada Fezzan Finance')
            ),
            subject: "Salary Slip Status Update - {$employeeName} - {$period}"
        );
    }

    public function content(): Content
    {
        $confirmUrl = null;
        $reportNotReceivedUrl = null;

        if ($this->salarySlip->status === SalarySlip::STATUS_SENT_TO_BANK) {
            $confirmUrl = URL::temporarySignedRoute(
                'salary-slips.confirm-receipt',
                now()->addDays(14),
                ['salarySlip' => $this->salarySlip->id]
            );

            $reportNotReceivedUrl = URL::temporarySignedRoute(
                'salary-slips.report-not-received',
                now()->addDays(14),
                ['salarySlip' => $this->salarySlip->id]
            );
        }

        return new Content(
            view: 'emails.salary-slip-status-updated',
            with: [
                'salarySlip' => $this->salarySlip,
                'oldStatusLabel' => $this->oldStatusLabel,
                'newStatusLabel' => $this->newStatusLabel,
                'printUrl' => route('salary-slips.print', ['salarySlip' => $this->salarySlip]),
                'confirmUrl' => $confirmUrl,
                'reportNotReceivedUrl' => $reportNotReceivedUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
