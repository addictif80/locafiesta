<?php

namespace App\Mail;

use App\Models\Reservation;
use App\Services\PdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use App\Mail\BaseMailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationContractMail extends BaseMailable
{
    use Queueable, SerializesModels;

    public function __construct(public Reservation $reservation) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Votre contrat de location — ' . $this->reservation->reference);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.reservation-contract');
    }

    public function attachments(): array
    {
        $pdf = app(PdfService::class)->generateContract($this->reservation);

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                'contrat-' . $this->reservation->reference . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
