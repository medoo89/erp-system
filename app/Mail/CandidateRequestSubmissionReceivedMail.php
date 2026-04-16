<?php

namespace App\Mail;

use App\Models\CandidateRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CandidateRequestSubmissionReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CandidateRequest $candidateRequest,
        public string $portalUrl,
    ) {
    }

    public function build(): static
    {
        return $this
            ->subject('We Received Your Submission')
            ->view('emails.candidate-request-submission-received');
    }
}