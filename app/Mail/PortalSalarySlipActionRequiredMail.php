<?php

namespace App\Mail;

use App\Models\SalarySlip;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PortalSalarySlipActionRequiredMail extends Mailable
{
    use Queueable, SerializesModels;

    public SalarySlip $salarySlip;

    public string $mailType;

    public string $portalUrl;

    public function __construct(SalarySlip $salarySlip, string $mailType = 'payment_confirmation')
    {
        $this->salarySlip = $salarySlip->loadMissing(['employment', 'client', 'project']);
        $this->mailType = $mailType;

        $this->portalUrl = route('portal.salary-slips.show', $this->salarySlip);
    }

    public function envelope(): Envelope
    {
        $employeeName = $this->salarySlip->employment?->employee_name ?: 'Employee';

        $period = ($this->salarySlip->salary_year ?: '-') . '-' . str_pad((string) ($this->salarySlip->salary_month ?: 0), 2, '0', STR_PAD_LEFT);

        $subject = $this->mailType === 'approved'
            ? "Salary Slip Approved - {$employeeName} - {$period}"
            : "Salary Payment Confirmation Required - {$employeeName} - {$period}";

        return new Envelope(
            from: new Address(
                env('FINANCE_MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS', 'hello@example.com')),
                env('FINANCE_MAIL_FROM_NAME', 'Sada Fezzan Finance')
            ),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.portal-salary-slip-action-required',
            with: [
                'salarySlip' => $this->salarySlip,
                'mailType' => $this->mailType,
                'portalUrl' => $this->portalUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
