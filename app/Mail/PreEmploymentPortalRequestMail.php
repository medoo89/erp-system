<?php

namespace App\Mail;

use App\Models\PreEmployment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PreEmploymentPortalRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public PreEmployment $preEmployment;
    public string $portalUrl;
    public bool $isUpdateRequest;

    public function __construct(PreEmployment $preEmployment, bool $isUpdateRequest = false)
    {
        $this->preEmployment = $preEmployment;
        $this->portalUrl = url('/pre-employment/portal/' . $preEmployment->portal_token);
        $this->isUpdateRequest = $isUpdateRequest;
    }

    public function build(): static
    {
        $subject = $this->isUpdateRequest
            ? 'Additional Pre-Employment Requirements'
            : 'Pre-Employment Portal Access';

        return $this->subject($subject)
            ->view('emails.pre-employment-portal-request');
    }
}