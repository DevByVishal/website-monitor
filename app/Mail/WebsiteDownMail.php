<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WebsiteDownMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $websiteUrl
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: 'do-not-reply@example.com',
            subject: "{$this->websiteUrl} is down!",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.website-down',
        );
    }
}