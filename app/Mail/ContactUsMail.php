<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactUsMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $contact;

    public function __construct(array $contact)
    {
        $this->contact = $contact;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Contact Us - ' . $this->contact['subject'],
            replyTo: $this->contact['email'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-us',
            with: [
                'contact' => $this->contact,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}