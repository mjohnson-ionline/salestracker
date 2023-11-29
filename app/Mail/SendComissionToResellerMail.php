<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class SendComissionToResellerMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct($reseller, $csv_path)
    {
        $this->reseller = $reseller;
        $this->subject = 'Your Comissions Report from iOnline for the Month ' . date('F Y');
        $this->first_name = $reseller->first_name;
        $this->last_name = $reseller->last_name;
        $this->email = $reseller->email;
        $this->organisation_name = $reseller->organisation_name;
        $this->csv_path = $csv_path;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('accounts@ionline.com.au', 'iOnline Sales and Commissions Tracker'),
            subject: $this->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.send-comission-to-reseller',
            with: [
                'reseller' => $this->reseller,
                'subject' => $this->subject,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'month' => date('F Y'),
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->csv_path),
        ];
    }
}
