<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    public string $contactName;
    public string $contactEmail;
    public string $serviceType;
    public string $messageBody;

    public function __construct(string $contactName, string $contactEmail, string $serviceType, string $messageBody)
    {
        $this->contactName = $contactName;
        $this->contactEmail = $contactEmail;
        $this->serviceType = $serviceType;
        $this->messageBody = $messageBody;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Contact Inquiry — {$this->serviceType}",
            replyTo: [new Address($this->contactEmail, $this->contactName)],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-message',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
