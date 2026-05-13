<?php

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobApplicationReceivedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public JobApplication $application,
        public array $answers = []
    ) {}

    public function build()
    {
        $jobTitle = $this->application->job?->title
            ?? $this->application->job_title
            ?? $this->application->position
            ?? 'Job Application';

        return $this
            ->subject('Application Received - ' . $jobTitle)
            ->view('emails.job-application-received')
            ->with([
                'application' => $this->application,
                'answers' => $this->answers,
                'jobTitle' => $jobTitle,
            ]);
    }
}
