<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use App\Mail\BaseMailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LateReturnAlert extends BaseMailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Reservation $reservation,
        public bool $isAdmin = false
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->isAdmin
            ? '[RETARD] Retour en retard — ' . $this->reservation->reference
            : 'Retour en retard — Votre location ' . $this->reservation->reference;

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.late-return-alert');
    }
}
