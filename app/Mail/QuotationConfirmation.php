<?php

namespace App\Mail;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuotationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public Quotation $quotation;
    public string $serviceLabel;
    private ?string $pdfPath;

    public function __construct(Quotation $quotation, ?string $pdfPath = null)
    {
        $this->quotation = $quotation;
        $this->pdfPath = $pdfPath;

        $serviceLabels = [
            'Deep Cleaning' => 'Deep Cleaning',
            'Final Cleaning' => 'Final Cleaning',
            'Daily Cleaning' => 'Daily Cleaning',
            'Snowout Cleaning' => 'Snowout Cleaning',
            'General Cleaning' => 'General Cleaning',
            'Hotel Cleaning' => 'Hotel Cleaning',
        ];

        $services = $quotation->cleaning_services ?? [];
        $this->serviceLabel = !empty($services) ? implode(', ', $services) : 'Cleaning Service';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Quotation Request Received — {$this->serviceLabel}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.quotation-confirmation',
        );
    }

    public function attachments(): array
    {
        if ($this->pdfPath && file_exists(storage_path('app/public/' . $this->pdfPath))) {
            return [
                Attachment::fromStorageDisk('public', $this->pdfPath)
                    ->as($this->serviceLabel . ' - Quotation.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
