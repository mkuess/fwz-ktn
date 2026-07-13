<?php

namespace App\Mail;

use App\Models\Organisation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrgRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Organisation $organisation)
    {
    }

    public function build()
    {
        return $this
            ->subject('Neue Vereinsregistrierung: '.$this->organisation->name)
            ->view('emails.org-registered');
    }
}
