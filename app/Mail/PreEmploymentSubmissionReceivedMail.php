<?php

namespace App\Mail;

use App\Models\PreEmployment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PreEmploymentSubmissionReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public PreEmployment $preEmployment;
    public string $portalUrl;

    public function __construct(PreEmployment $preEmployment)
    {
        $this->preEmployment = $preEmployment;
        $this->portalUrl = url('/pre-employment/portal/' . $preEmployment->portal_token);
    }

    public function build(): static
    {
        return $this->subject('We Received Your Pre-Employment Submission')
            ->view('emails.pre-employment-submission-received');
    }
}