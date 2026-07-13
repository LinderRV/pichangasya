@extends('emails.layout')

@section('titulo', 'Reserva cancelada')

@section('contenido')
    <h2 style="margin:0 0 12px;color:#b91c1c;font-size:20px;">Tu reserva fue cancelada</h2>
    <p style="margin:0 0 20px;font-size:14px;line-height:1.5;">
        Hola {{ trim(($reserva->cliente->usuario->nombres ?? '') . ' ' . ($reserva->cliente->usuario->apellidos ?? '')) ?: 'cliente' }},
        el complejo canceló tu reserva y se registró tu reembolso. Estos son los detalles:
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e5e7eb;border-radius:6px;margin-bottom:20px;font-size:13.5px;">
        <tr><td style="padding:10px 16px;border-bottom:1px solid #e5e7eb;color:#4b5563;">Código de reserva</td><td style="padding:10px 16px;border-bottom:1px solid #e5e7eb;text-align:right;"><strong>{{ $reserva->codigo_reserva }}</strong></td></tr>
        <tr><td style="padding:10px 16px;border-bottom:1px solid #e5e7eb;color:#4b5563;">Complejo</td><td style="padding:10px 16px;border-bottom:1px solid #e5e7eb;text-align:right;">{{ $reserva->cancha->complejo->nombre ?? '-' }}</td></tr>
        <tr><td style="padding:10px 16px;border-bottom:1px solid #e5e7eb;color:#4b5563;">Cancha</td><td style="padding:10px 16px;border-bottom:1px solid #e5e7eb;text-align:right;">{{ $reserva->cancha->nombre ?? '-' }}</td></tr>
        <tr><td style="padding:10px 16px;border-bottom:1px solid #e5e7eb;color:#4b5563;">Fecha reservada</td><td style="padding:10px 16px;border-bottom:1px solid #e5e7eb;text-align:right;">{{ \Carbon\Carbon::parse($reserva->fecha_reserva)->translatedFormat('d/m/Y') }}</td></tr>
        <tr><td style="padding:10px 16px;border-bottom:1px solid #e5e7eb;color:#4b5563;">Horario</td><td style="padding:10px 16px;border-bottom:1px solid #e5e7eb;text-align:right;">{{ substr($reserva->hora_inicio, 0, 5) }} - {{ substr($reserva->hora_fin, 0, 5) }}</td></tr>
        <tr><td style="padding:10px 16px;border-bottom:1px solid #e5e7eb;color:#4b5563;">Motivo</td><td style="padding:10px 16px;border-bottom:1px solid #e5e7eb;text-align:right;">{{ $reserva->motivo_cancelacion }}</td></tr>
        <tr><td style="padding:10px 16px;color:#4b5563;">Reembolso ({{ ucfirst($metodoReembolso) }})</td><td style="padding:10px 16px;text-align:right;"><strong>S/ {{ number_format($montoReembolso, 2) }}</strong></td></tr>
    </table>

    <p style="margin:0;font-size:13px;color:#4b5563;line-height:1.5;">
        Si tienes dudas sobre tu reembolso, contacta directamente al complejo.
    </p>
@endsection
