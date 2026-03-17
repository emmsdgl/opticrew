<?php

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationWithdrawnFarewell extends Mailable
{
    use Queueable, SerializesModels;

    public JobApplication $application;

    public function __construct(JobApplication $application)
    {
        $this->application = $application;
    }

    public function envelope()
    {
        return new Envelope(
            subject: "We're sorry to see you go - {$this->application->job_title}",
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.application-withdrawn-farewell',
        );
    }

    public function attachments()
    {
        return [];
    }
}
