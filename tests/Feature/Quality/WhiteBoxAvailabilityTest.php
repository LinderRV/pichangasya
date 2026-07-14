<?php

namespace Tests\Feature\Quality;

use App\Models\Cancha;
use App\Models\EstadoReserva;
use App\Services\DisponibilidadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class WhiteBoxAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    private Cancha $cancha;
    private DisponibilidadService $service;
    private string $fecha = '2030-07-15';

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(DisponibilidadService::class);
        $this->seedMinimumDomain();
        $this->cancha = Cancha::findOrFail(1);
    }

    public function test_only_documented_booking_durations_are_accepted(): void
    {
        $this->assertTrue(DisponibilidadService::duracionValida(60));
        $this->assertTrue(DisponibilidadService::duracionValida(180));
        $this->assertFalse(DisponibilidadService::duracionValida(45));
        $this->assertFalse(DisponibilidadService::duracionValida(181));
    }

    public function test_a_court_without_an_active_schedule_has_no_slots(): void
    {
        $this->assertSame([], $this->service->slotsDisponibles($this->cancha, $this->fecha));
    }

    public function test_active_schedule_generates_slots_with_the_expected_price(): void
    {
        $this->createSchedule();

        $slots = $this->service->slotsDisponibles($this->cancha, $this->fecha, 60);

        $this->assertCount(3, $slots);
        $this->assertSame('18:00', $slots[0]['hora_inicio']);
        $this->assertSame('19:00', $slots[0]['hora_fin']);
        $this->assertSame(50.0, $slots[0]['total']);
    }

    public function test_a_blocked_period_is_removed_from_availability(): void
    {
        $this->createSchedule();
        DB::table('bloqueo_canchas')->insert([
            'id_cancha' => 1,
            'fecha' => $this->fecha,
            'hora_inicio' => '19:00',
            'hora_fin' => '20:00',
            'motivo' => 'mantenimiento',
        ]);

        $slots = $this->service->slotsDisponibles($this->cancha, $this->fecha, 60);

        $this->assertSame(['18:00', '20:00'], array_column($slots, 'hora_inicio'));
    }

    public function test_a_confirmed_reservation_occupies_its_period(): void
    {
        $this->createSchedule();
        $this->createReservation(EstadoReserva::CONFIRMADA);

        $slots = $this->service->slotsDisponibles($this->cancha, $this->fecha, 60);

        $this->assertSame(['18:00', '20:00'], array_column($slots, 'hora_inicio'));
    }

    public function test_a_cancelled_reservation_releases_its_period(): void
    {
        $this->createSchedule();
        $this->createReservation(EstadoReserva::CANCELADA);

        $slots = $this->service->slotsDisponibles($this->cancha, $this->fecha, 60);

        $this->assertSame(['18:00', '19:00', '20:00'], array_column($slots, 'hora_inicio'));
    }

    public function test_overlapping_block_removes_every_affected_slot(): void
    {
        $this->createSchedule();
        DB::table('bloqueo_canchas')->insert([
            'id_cancha' => 1,
            'fecha' => $this->fecha,
            'hora_inicio' => '18:30',
            'hora_fin' => '20:30',
            'motivo' => 'evento_especial',
        ]);

        $slots = $this->service->slotsDisponibles($this->cancha, $this->fecha, 60);

        $this->assertSame([], $slots);
    }

    private function seedMinimumDomain(): void
    {
        DB::table('departamentos')->insert(['id' => 1, 'nombre' => 'Lima']);
        DB::table('provincias')->insert(['id' => 1, 'id_departamento' => 1, 'nombre' => 'Lima']);
        DB::table('distritos')->insert(['id' => 1, 'id_provincia' => 1, 'nombre' => 'Distrito de prueba']);
        DB::table('complejo_deportivos')->insert([
            'id' => 1,
            'id_distrito' => 1,
            'nombre' => 'Complejo de prueba',
            'correo' => 'complejo@example.test',
        ]);
        DB::table('tipo_canchas')->insert(['id' => 1, 'nombre' => 'Fútbol']);
        DB::table('canchas')->insert([
            'id' => 1,
            'id_complejo' => 1,
            'id_tipo_cancha' => 1,
            'nombre' => 'Cancha de prueba',
            'precio_hora' => 50,
        ]);
        DB::table('usuarios')->insert([
            'id' => 1,
            'nombres' => 'Cliente',
            'apellidos' => 'Prueba',
            'email' => 'cliente@example.test',
            'password' => 'no-usada',
        ]);
        DB::table('clientes')->insert([
            'id' => 1,
            'id_usuario' => 1,
            'documento_identidad' => 'DOC-PRUEBA',
        ]);
        DB::table('estado_reservas')->insert([
            ['id' => 1, 'nombre' => 'Confirmada'],
            ['id' => 2, 'nombre' => 'Completada'],
            ['id' => 3, 'nombre' => 'Cancelada'],
        ]);
    }

    private function createSchedule(): void
    {
        DB::table('horario_configurados')->insert([
            'id_cancha' => 1,
            'dia_semana' => 'Lunes',
            'hora_inicio' => '18:00',
            'hora_fin' => '21:00',
            'intervalo_minutos' => 60,
            'estado' => 'activo',
        ]);
    }

    private function createReservation(int $estado): void
    {
        DB::table('reservas')->insert([
            'codigo_reserva' => 'RES-PRUEBA',
            'id_cliente' => 1,
            'id_cancha' => 1,
            'id_estado_reserva' => $estado,
            'fecha_reserva' => $this->fecha,
            'hora_inicio' => '19:00',
            'hora_fin' => '20:00',
            'precio_hora' => 50,
            'subtotal' => 50,
            'total' => 50,
            'confirmado_at' => '2030-07-01 10:00:00',
        ]);
    }
}
