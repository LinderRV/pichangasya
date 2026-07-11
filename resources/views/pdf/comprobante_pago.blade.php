<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
    @page { margin: 24px 28px; }
    * { box-sizing: border-box; }
    body { font-family: 'Helvetica', Arial, sans-serif; color: #111827; font-size: 12px; }

    .header { width: 100%; border-bottom: 2px solid #198754; padding-bottom: 10px; margin-bottom: 14px; }
    .header table { width: 100%; }
    .logo img { height: 30px; }
    .titulo { text-align: right; }
    .titulo .doc { font-size: 15px; font-weight: bold; color: #1c5739; }
    .titulo .codigo { font-size: 11px; color: #111827; }

    .box { border: 1px solid #e5e7eb; border-radius: 4px; padding: 10px 12px; margin-bottom: 12px; }
    .box h3 { margin: 0 0 6px; font-size: 11px; text-transform: uppercase; color: #198754; letter-spacing: .5px; }
    .box p { margin: 2px 0; font-size: 11.5px; }

    .row-2 { width: 100%; }
    .row-2 td { vertical-align: top; width: 50%; padding-right: 8px; }

    table.detalle { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    table.detalle th { background: #198754; color: #fff; font-size: 10.5px; text-transform: uppercase; padding: 7px 8px; text-align: left; }
    table.detalle td { padding: 7px 8px; border-bottom: 1px solid #e5e7eb; font-size: 11.5px; }

    .totales { width: 100%; margin-top: 4px; }
    .totales td { padding: 3px 8px; font-size: 12px; }
    .totales .label { text-align: right; color: #4b5563; }
    .totales .valor { text-align: right; width: 110px; }
    .totales .total-final .label,
    .totales .total-final .valor { font-size: 14px; font-weight: bold; color: #1c5739; border-top: 2px solid #198754; padding-top: 6px; }

    .badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 10.5px; font-weight: bold; color: #fff; }
    .badge-confirmado { background: #198754; }
    .badge-reembolsado { background: #6c757d; }
    .badge-anulado { background: #dc3545; }

    .footer { margin-top: 24px; padding-top: 10px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 9.5px; color: #6b7280; }
</style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td class="logo">
                    <img src="{{ public_path('images/logo-text.png') }}">
                </td>
                <td class="titulo">
                    <div class="doc">COMPROBANTE DE PAGO</div>
                    <div class="codigo">N.° Operación: {{ $pago->codigo_operacion }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="row-2">
        <tr>
            <td>
                <div class="box">
                    <h3>Establecimiento</h3>
                    <p><strong>{{ $complejo->nombre ?? '-' }}</strong></p>
                    @if($complejo?->ruc)
                        <p>RUC: {{ $complejo->ruc }}</p>
                    @endif
                    @if($complejo?->direccion)
                        <p>{{ $complejo->direccion }}</p>
                    @endif
                    @if($complejo?->telefono)
                        <p>Tel: {{ $complejo->telefono }}</p>
                    @endif
                </div>
            </td>
            <td>
                <div class="box">
                    <h3>Cliente</h3>
                    <p><strong>{{ $clienteNombre }}</strong></p>
                    @if($clienteDocumento)
                        <p>Documento: {{ $clienteDocumento }}</p>
                    @endif
                    @if($clienteEmail)
                        <p>{{ $clienteEmail }}</p>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <table class="detalle">
        <thead>
            <tr>
                <th>Cancha</th>
                <th>Fecha</th>
                <th>Horario</th>
                <th>Precio / hora</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $reserva->cancha->nombre ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($reserva->fecha_reserva)->format('d/m/Y') }}</td>
                <td>{{ substr($reserva->hora_inicio, 0, 5) }} - {{ substr($reserva->hora_fin, 0, 5) }}</td>
                <td>S/ {{ number_format($reserva->precio_hora, 2) }}</td>
                <td>S/ {{ number_format($reserva->total, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="row-2">
        <tr>
            <td>
                <div class="box">
                    <h3>Detalle del pago</h3>
                    <p>Método de pago: <strong>{{ $pago->metodoPago->nombre ?? '-' }}</strong></p>
                    <p>Fecha de pago: {{ optional($pago->fecha_pago)->format('d/m/Y H:i') }}</p>
                    <p>Estado:
                        <span class="badge badge-{{ $pago->estado }}">{{ strtoupper($pago->estado) }}</span>
                    </p>
                </div>
            </td>
            <td>
                <table class="totales">
                    <tr>
                        <td class="label">Subtotal</td>
                        <td class="valor">S/ {{ number_format($reserva->subtotal, 2) }}</td>
                    </tr>
                    <tr class="total-final">
                        <td class="label">Total pagado</td>
                        <td class="valor">S/ {{ number_format($pago->monto, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="footer">
        Comprobante generado por PichangasYa · Código de reserva: {{ $reserva->codigo_reserva }}<br>
        Este documento es un comprobante interno de la transacción, no constituye boleta ni factura electrónica SUNAT.
    </div>

</body>
</html>
