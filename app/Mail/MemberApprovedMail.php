<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MemberApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $code,
        public readonly string $resetUrl,
        public readonly int $lifetimeDays,
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Dein Zugang zum Freiwilligenzentrum wurde freigeschaltet')
            ->view('emails.member-approved');
    }
}
