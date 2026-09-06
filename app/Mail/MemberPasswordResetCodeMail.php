<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MemberPasswordResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public readonly string $code;

    public readonly int $lifetimeMinutes;

    public function __construct(
        string $code,
        int $lifetimeMinutes,
    ) {
        $this->code = $code;
        $this->lifetimeMinutes = $lifetimeMinutes;
    }

    public function build(): self
    {
        return $this
            ->subject('Dein Code zum Zurücksetzen des Passworts')
            ->view('emails.member-password-reset-code');
    }
}
