<?php

namespace App\Mail;

use App\Models\CandidateRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CandidateRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CandidateRequest $candidateRequest,
        public string $portalUrl,
    ) {
    }

    public function build(): static
    {
        $jobApplication = $this->candidateRequest->jobApplication;
        $jobTitle = optional($jobApplication?->job)->title ?: '-';

        return $this
            ->subject('New Request for Your Job Application')
            ->view('emails.candidate-request')
            ->with([
                'mailEyebrow' => 'Candidate Request',
                'mailTitle' => 'New Request for Your Job Application',
                'mailIntro' => 'Please review the request details below and use the portal link to respond or upload the requested files.',
                'mailBadgeText' => 'Portal Access Required',
                'mailAccent' => '#26b6b7',
                'mailButtonText' => 'Open Request Portal',
                'mailButtonUrl' => $this->portalUrl,
                'mailFooter' => 'Sada Fezzan Recruitment Team',
                'jobTitle' => $jobTitle,
            ]);
    }
}