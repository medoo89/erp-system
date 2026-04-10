<?php

namespace App\Mail;

use App\Models\PreEmployment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PreEmploymentStartedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public PreEmployment $preEmployment;

    public function __construct(PreEmployment $preEmployment)
    {
        $this->preEmployment = $preEmployment;
    }

    public function build(): self
    {
        return $this
            ->subject('Pre-Employment Process Started')
            ->view('emails.pre-employment-started')
            ->with([
                'preEmployment' => $this->preEmployment,
            ]);
    }
}