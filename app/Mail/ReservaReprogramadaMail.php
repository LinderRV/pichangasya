<?php

namespace App\Mail;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservaReprogramadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Reserva $reserva,
        public string $fechaAnterior,
        public string $horaInicioAnterior,
        public string $horaFinAnterior,
        public string $motivo,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reserva reprogramada - ' . $this->reserva->codigo_reserva,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reserva_reprogramada',
        );
    }
}
