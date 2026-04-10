<?php

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobApplicationSubmittedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public JobApplication $jobApplication;
    public array $applicationAnswers;

    public function __construct(JobApplication $jobApplication, array $applicationAnswers = [])
    {
        $this->jobApplication = $jobApplication;
        $this->applicationAnswers = $applicationAnswers;
    }

    public function build(): self
    {
        return $this
            ->subject('Your Job Application Has Been Received')
            ->view('emails.job-application-submitted')
            ->with([
                'jobApplication' => $this->jobApplication,
                'applicationAnswers' => $this->applicationAnswers,
            ]);
    }
}