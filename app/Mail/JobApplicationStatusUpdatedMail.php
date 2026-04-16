<?php

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobApplicationStatusUpdatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public JobApplication $jobApplication;
    public string $statusLabel;
    public string $subjectLine;
    public string $messageBody;

    public function __construct(
        JobApplication $jobApplication,
        string $statusLabel,
        string $subjectLine,
        string $messageBody,
    ) {
        $this->jobApplication = $jobApplication;
        $this->statusLabel = $statusLabel;
        $this->subjectLine = $subjectLine;
        $this->messageBody = $messageBody;
    }

    public function build(): self
    {
        return $this
            ->subject($this->subjectLine)
            ->view('emails.job-application-status-updated')
            ->with([
                'jobApplication' => $this->jobApplication,
                'statusLabel' => $this->statusLabel,
                'subjectLine' => $this->subjectLine,
                'messageBody' => $this->messageBody,
            ]);
    }
}