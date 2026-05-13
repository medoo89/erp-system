<?php

namespace App\Mail;

use App\Models\Employment;
use App\Models\PortalAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PortalAccountAccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PortalAccount $portalAccount,
        public Employment $employment,
        public string $setupUrl,
        public string $mailType = 'setup',
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->mailType === 'reset'
            ? 'Set a New Employee Portal Password'
            : 'Set Up Your Employee Portal Password';

        return new Envelope(
            from: new Address(
                env('PORTAL_MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS', 'hello@example.com')),
                env('PORTAL_MAIL_FROM_NAME', env('MAIL_FROM_NAME', 'Sada Fezzan Portal'))
            ),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.portal-account-access',
            with: [
                'portalAccount' => $this->portalAccount,
                'employment' => $this->employment,
                'setupUrl' => $this->setupUrl,
                'mailType' => $this->mailType,
                'portalLoginUrl' => url('/portal/login'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
