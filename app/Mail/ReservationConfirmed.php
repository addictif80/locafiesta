<?php

namespace App\Mail;

use App\Models\Reservation;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use App\Mail\BaseMailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationConfirmed extends BaseMailable
{
    use Queueable, SerializesModels;

    public function __construct(public Reservation $reservation) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Confirmation de votre réservation - ' . $this->reservation->reference);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.reservation-confirmed');
    }
}
