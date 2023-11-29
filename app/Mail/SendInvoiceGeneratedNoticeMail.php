<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendInvoiceGeneratedNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    private $organisation_name;
    private $contact_name;
    private $contact_email;
    private $deal_parts;

    public function __construct($organisation_name, $contact_name, $contact_email, $deal_parts)
    {
        $this->organisation_name = $organisation_name;
        $this->contact_name = $contact_name;
        $this->contact_email = $contact_email;
        $this->deal_parts = $deal_parts;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('doNotreply@ionline.com.au', 'Your Friendly Proposal App'),
            subject: 'Invoices for a New Deal Have Been Generated',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.send-invoice-generated-notice',
            with: [
                'organisation_name' => $this->organisation_name,
                'contact_name' => $this->contact_name,
                'contact_email' => $this->contact_email,
                'deal_parts' => $this->deal_parts,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
