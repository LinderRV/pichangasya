<?php

namespace App\Services;

use App\Mail\ReservaConfirmadaMail;
use App\Models\Cancha;
use App\Models\EstadoReserva;
use App\Models\HistorialEstadoReserva;
use App\Models\MetodoPago;
use App\Models\Pago;
use App\Models\Reserva;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Exception;

class ReservaPagoService
{
    public function __construct(private DisponibilidadService $disponibilidad) {}

    /**
     * Confirma la reserva a partir de los metadatos de una Checkout Session de Stripe.
     * Idempotente: si ya existe un Pago con ese codigo_operacion, devuelve la reserva existente
     * sin volver a procesar (protege contra reintentos de webhook y el fallback de la success_url).
     */
    public function confirmarPagoStripe(array $metadata, string $codigoOperacion): ?Reserva
    {
        $pagoExistente = Pago::where('codigo_operacion', $codigoOperacion)->first();
        if ($pagoExistente) {
            return $pagoExistente->reserva;
        }

        $cancha = Cancha::find($metadata['id_cancha']);
        if (!$cancha) {
            return null;
        }

        $duracionMinutos = (int) ((strtotime($metadata['hora_fin']) - strtotime($metadata['hora_inicio'])) / 60);

        $metodoPago = MetodoPago::firstOrCreate(
            ['nombre' => 'Stripe'],
            ['estado' => 'activo']
        );

        $reserva = DB::transaction(function () use ($metadata, $cancha, $duracionMinutos, $codigoOperacion, $metodoPago) {
            Cancha::where('id', $cancha->id)->lockForUpdate()->first();

            $pagoExistente = Pago::where('codigo_operacion', $codigoOperacion)->first();
            if ($pagoExistente) {
                return $pagoExistente->reserva;
            }

            $slots = $this->disponibilidad->slotsDisponibles($cancha, $metadata['fecha'], $duracionMinutos);
            $slotValido = collect($slots)->first(fn($s) =>
                $s['hora_inicio'] === $metadata['hora_inicio'] &&
                $s['hora_fin']    === $metadata['hora_fin']
            );

            if (!$slotValido) {
                return null;
            }

            $ahora = now();

            $reserva = Reserva::create([
                'codigo_reserva'    => $this->generarCodigo(),
                'id_cliente'        => $metadata['id_cliente'],
                'id_cancha'         => $cancha->id,
                'id_estado_reserva' => EstadoReserva::CONFIRMADA,
                'fecha_reserva'     => $metadata['fecha'],
                'hora_inicio'       => $metadata['hora_inicio'],
                'hora_fin'          => $metadata['hora_fin'],
                'precio_hora'       => $metadata['precio_hora'],
                'subtotal'          => $metadata['total'],
                'total'             => $metadata['total'],
                'confirmado_at'     => $ahora,
            ]);

            Pago::create([
                'id_reserva'       => $reserva->id,
                'id_metodo_pago'   => $metodoPago->id,
                'codigo_operacion' => $codigoOperacion,
                'monto'            => $metadata['total'],
                'comprobante_url'  => null,
                'estado'           => 'confirmado',
                'fecha_pago'       => $ahora,
            ]);

            HistorialEstadoReserva::create([
                'id_reserva'        => $reserva->id,
                'id_estado_reserva' => EstadoReserva::CONFIRMADA,
                'id_usuario'        => $metadata['id_usuario'],
                'fecha_cambio'      => $ahora,
                'observacion'       => 'Reserva confirmada vía Stripe. Sesión: ' . $codigoOperacion,
            ]);

            return $reserva;
        });

        if ($reserva) {
            $this->enviarCorreoConfirmacion($reserva);
        }

        return $reserva;
    }

    private function enviarCorreoConfirmacion(Reserva $reserva): void
    {
        try {
            $reserva->load(['cliente.usuario', 'cancha.complejo']);
            $email = optional($reserva->cliente->usuario)->email;
            if ($email) {
                Mail::to($email)->send(new ReservaConfirmadaMail($reserva));
            }
        } catch (Exception $e) {
            Log::error('No se pudo enviar correo de confirmación de reserva: ' . $e->getMessage());
        }
    }

    private function generarCodigo(): string
    {
        do {
            $codigo = 'PY-' . date('ym') . strtoupper(Str::random(5));
        } while (Reserva::where('codigo_reserva', $codigo)->exists());
        return $codigo;
    }
}
