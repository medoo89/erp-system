<?php

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobApplicationDeclinedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public JobApplication $jobApplication;
    public string $declineReasonLabel;
    public ?string $declineNotes;

    public function __construct(
        JobApplication $jobApplication,
        string $declineReasonLabel,
        ?string $declineNotes = null,
    ) {
        $this->jobApplication = $jobApplication;
        $this->declineReasonLabel = $declineReasonLabel;
        $this->declineNotes = $declineNotes;
    }

    public function build(): self
    {
        return $this
            ->subject('Update on Your Job Application')
            ->view('emails.job-application-declined');
    }
}