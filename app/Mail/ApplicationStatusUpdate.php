<?php

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public JobApplication $application;
    public string $statusLabel;
    public string $statusMessage;

    public function __construct(JobApplication $application)
    {
        $this->application = $application;

        $this->statusLabel = match ($application->status) {
            'reviewed' => 'Under Review',
            'interview_scheduled' => 'Interview Scheduled',
            'hired' => 'Hired',
            'rejected' => 'Not Selected',
            default => 'Updated',
        };

        $this->statusMessage = match ($application->status) {
            'reviewed' => "We wanted to let you know that your application for <strong>{$application->job_title}</strong> is currently being reviewed by our team at Finnoys. We appreciate your interest and will get back to you shortly.",
            'interview_scheduled' => "Great news! Your application for <strong>{$application->job_title}</strong> has moved to the interview stage. Our team will reach out to you soon with the interview details.",
            'hired' => "Congratulations! We are pleased to inform you that you have been selected for the <strong>{$application->job_title}</strong> position at Finnoys. Our team will contact you with the next steps.",
            'rejected' => "Thank you for your interest in the <strong>{$application->job_title}</strong> position at Finnoys. After careful review, we have decided to move forward with other candidates. We encourage you to apply for future openings.",
            default => "Your application status for <strong>{$application->job_title}</strong> has been updated.",
        };
    }

    public function envelope()
    {
        return new Envelope(
            subject: "Application Update: {$this->statusLabel} - {$this->application->job_title}",
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.application-status-update',
        );
    }

    public function attachments()
    {
        return [];
    }
}
