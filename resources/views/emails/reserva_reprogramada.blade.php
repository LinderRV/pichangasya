@extends('emails.layout')

@section('titulo', 'Reserva reprogramada')

@section('contenido')
    <h2 style="margin:0 0 12px;color:#b45309;font-size:20px;">Tu reserva fue reprogramada</h2>
    <p style="margin:0 0 20px;font-size:14px;line-height:1.5;">
        Hola {{ trim(($reserva->cliente->usuario->nombres ?? '') . ' ' . ($reserva->cliente->usuario->apellidos ?? '')) ?: 'cliente' }},
        el complejo cambió el horario de tu reserva <strong>{{ $reserva->codigo_reserva }}</strong>. Estos son los detalles:
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e5e7eb;border-radius:6px;margin-bottom:16px;font-size:13.5px;">
        <tr><td style="padding:10px 16px;border-bottom:1px solid #e5e7eb;color:#4b5563;">Complejo</td><td style="padding:10px 16px;border-bottom:1px solid #e5e7eb;text-align:right;">{{ $reserva->cancha->complejo->nombre ?? '-' }}</td></tr>
        <tr><td style="padding:10px 16px;border-bottom:1px solid #e5e7eb;color:#4b5563;">Cancha</td><td style="padding:10px 16px;border-bottom:1px solid #e5e7eb;text-align:right;">{{ $reserva->cancha->nombre ?? '-' }}</td></tr>
        <tr><td style="padding:10px 16px;border-bottom:1px solid #e5e7eb;color:#9ca3af;text-decoration:line-through;">Horario anterior</td><td style="padding:10px 16px;border-bottom:1px solid #e5e7eb;text-align:right;color:#9ca3af;text-decoration:line-through;">{{ \Carbon\Carbon::parse($fechaAnterior)->translatedFormat('d/m/Y') }} · {{ substr($horaInicioAnterior, 0, 5) }} - {{ substr($horaFinAnterior, 0, 5) }}</td></tr>
        <tr><td style="padding:10px 16px;color:#1c5739;"><strong>Nuevo horario</strong></td><td style="padding:10px 16px;text-align:right;color:#1c5739;"><strong>{{ \Carbon\Carbon::parse($reserva->fecha_reserva)->translatedFormat('d/m/Y') }} · {{ substr($reserva->hora_inicio, 0, 5) }} - {{ substr($reserva->hora_fin, 0, 5) }}</strong></td></tr>
    </table>

    <p style="margin:0 0 20px;font-size:13px;color:#4b5563;line-height:1.5;">
        <strong>Motivo:</strong> {{ $motivo }}
    </p>

    <p style="margin:0;font-size:13px;color:#4b5563;line-height:1.5;">
        Si el nuevo horario no te funciona, contacta directamente al complejo.
    </p>
@endsection
