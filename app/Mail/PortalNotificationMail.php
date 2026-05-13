<?php

namespace App\Mail;

use App\Models\PortalAccount;
use App\Models\PortalNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PortalNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PortalAccount $portalAccount,
        public PortalNotification $notification,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                env('PORTAL_MAIL_FROM_ADDRESS', env('MAIL_FROM_ADDRESS', 'hello@example.com')),
                env('PORTAL_MAIL_FROM_NAME', env('MAIL_FROM_NAME', 'Sada Fezzan Portal'))
            ),
            subject: $this->notification->title ?: 'Portal Notification',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.portal-notification',
            with: [
                'portalAccount' => $this->portalAccount,
                'notification' => $this->notification,
                'portalUrl' => url('/portal'),
                'actionUrl' => $this->notification->action_url ?: url('/portal/notifications'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
