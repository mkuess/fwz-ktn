<?php

namespace App\Mail;

use App\Models\Organisation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrgRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Organisation $organisation) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Neue Organisation registriert: ' . $this->organisation->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.org-registered',
        );
    }
}
