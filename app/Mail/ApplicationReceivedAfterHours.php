<?php

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationReceivedAfterHours extends Mailable
{
    use Queueable, SerializesModels;

    public JobApplication $application;
    public string $responseEta;

    public function __construct(JobApplication $application, string $responseEta)
    {
        $this->application = $application;
        $this->responseEta = $responseEta;
    }

    public function envelope()
    {
        return new Envelope(
            subject: "Application Received - {$this->application->job_title}",
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.application-received-after-hours',
        );
    }

    public function attachments()
    {
        return [];
    }
}
