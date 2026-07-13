<?php

namespace App\Mail;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservaCanceladaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Reserva $reserva,
        public string $metodoReembolso,
        public float $montoReembolso,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reserva cancelada - ' . $this->reserva->codigo_reserva,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reserva_cancelada',
        );
    }
}
