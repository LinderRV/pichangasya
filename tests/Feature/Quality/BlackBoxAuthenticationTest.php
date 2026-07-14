<?php

namespace Tests\Feature\Quality;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class BlackBoxAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('roles')->insert([
            ['id' => 1, 'nombre' => 'Super Admin', 'descripcion' => 'Administración'],
            ['id' => 2, 'nombre' => 'Usuario Interno', 'descripcion' => 'Personal'],
            ['id' => 3, 'nombre' => 'Cliente', 'descripcion' => 'Cliente final'],
        ]);
    }

    public function test_login_page_is_available_to_a_visitor(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertHeader('Content-Security-Policy')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertCookieMissing('XSRF-TOKEN');
    }

    public function test_public_home_and_institutional_pages_are_available(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Cómo funciona')
            ->assertSee(route('web.paginas.terminos'), false)
            ->assertSee(route('web.paginas.privacidad'), false)
            ->assertSee(route('web.paginas.ayuda'), false);

        $this->get('/terminos-y-condiciones')->assertOk()->assertSee('Términos y condiciones de uso');
        $this->get('/politica-de-privacidad')->assertOk()->assertSee('Política de privacidad y cookies');
        $this->get('/ayuda')->assertOk()->assertSee('Centro de ayuda');
    }

    public function test_super_admin_dashboard_displays_actionable_business_metrics(): void
    {
        $usuario = $this->createUsuario('activo');
        DB::table('usuario_rol')->insert(['id_usuario' => $usuario->id, 'id_rol' => 1]);

        $this->actingAs($usuario)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Ticket Promedio')
            ->assertSee('Tasa de Cancelación')
            ->assertSee('Reembolsos del Mes')
            ->assertSee('Evolución de los últimos 6 meses');
    }

    public function test_client_can_view_sanitized_detail_of_own_reservation(): void
    {
        $usuario = $this->createUsuario('activo');
        DB::table('usuario_rol')->insert(['id_usuario' => $usuario->id, 'id_rol' => 3]);
        $reservaId = $this->seedReservationDomain($usuario->id);

        $this->actingAs($usuario)
            ->getJson('/cliente/reservas/' . $reservaId)
            ->assertOk()
            ->assertJsonPath('data.codigo', 'RES-DETALLE')
            ->assertJsonPath('data.estado', 'Confirmada')
            ->assertJsonPath('data.historial.0.estado', 'Confirmada')
            ->assertJsonMissing(['observacion' => 'Dato interno no visible']);
    }

    public function test_client_cannot_view_another_clients_reservation(): void
    {
        $propietario = $this->createUsuario('activo');
        DB::table('usuario_rol')->insert(['id_usuario' => $propietario->id, 'id_rol' => 3]);
        $reservaId = $this->seedReservationDomain($propietario->id);

        $otro = Usuario::create([
            'nombres' => 'Otro',
            'apellidos' => 'Cliente',
            'email' => 'otro@example.test',
            'password' => Hash::make('Password123!'),
            'estado' => 'activo',
        ]);
        DB::table('usuario_rol')->insert(['id_usuario' => $otro->id, 'id_rol' => 3]);
        DB::table('clientes')->insert(['id_usuario' => $otro->id, 'documento_identidad' => 'DOC-OTRO']);

        $this->actingAs($otro)->getJson('/cliente/reservas/' . $reservaId)->assertNotFound();
    }

    public function test_password_recovery_page_does_not_disclose_an_application_error(): void
    {
        $this->get('/forgot-password')
            ->assertOk()
            ->assertHeader('Content-Security-Policy');
    }

    public function test_non_ajax_invalid_login_returns_json_instead_of_a_large_redirect(): void
    {
        $this->post('/login', [])->assertUnprocessable()->assertJsonValidationErrors([
            'email',
            'password',
        ]);
    }

    public function test_password_reset_request_returns_a_small_generic_json_response(): void
    {
        $this->post('/forgot-password', ['email' => 'nadie@example.test'])
            ->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonMissingValidationErrors('email');
    }

    public function test_login_rejects_empty_fields(): void
    {
        $this->postJson('/login', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_rejects_an_invalid_password(): void
    {
        $usuario = $this->createUsuario('activo');

        $this->postJson('/login', [
            'email' => $usuario->email,
            'password' => 'incorrecta',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');

        $this->assertGuest();
    }

    public function test_login_rejects_an_inactive_account(): void
    {
        $usuario = $this->createUsuario('inactivo');

        $this->postJson('/login', [
            'email' => $usuario->email,
            'password' => 'Password123!',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');

        $this->assertGuest();
    }

    public function test_active_client_can_login_and_receives_profile_destination(): void
    {
        $usuario = $this->createUsuario('activo');
        DB::table('usuario_rol')->insert([
            'id_usuario' => $usuario->id,
            'id_rol' => 3,
        ]);

        $this->postJson('/login', [
            'email' => $usuario->email,
            'password' => 'Password123!',
        ])->assertOk()->assertJsonPath('data.redirect', '/cliente/perfil');

        $this->assertAuthenticatedAs($usuario);
    }

    public function test_registration_rejects_a_short_password_and_mismatched_confirmation(): void
    {
        $this->postJson('/register', [
            'nombres' => 'Ana',
            'apellidos' => 'Prueba',
            'email' => 'ana@example.test',
            'clave' => '123',
            'clave_confirmation' => '456',
        ])->assertUnprocessable()->assertJsonValidationErrors(['clave', 'clave_confirmation']);
    }

    public function test_valid_client_registration_creates_an_authenticated_client(): void
    {
        $this->postJson('/register', [
            'nombres' => 'Ana',
            'apellidos' => 'Prueba',
            'email' => 'ana@example.test',
            'clave' => 'Password123!',
            'clave_confirmation' => 'Password123!',
        ])->assertOk()->assertJsonPath('data.redirect', '/cliente/perfil');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('usuarios', ['email' => 'ana@example.test']);
        $this->assertDatabaseHas('usuario_rol', ['id_rol' => 3]);
    }

    public function test_guest_cannot_access_the_administrative_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    private function createUsuario(string $estado): Usuario
    {
        RateLimiter::clear('prueba@example.test|127.0.0.1');

        return Usuario::create([
            'nombres' => 'Usuario',
            'apellidos' => 'Prueba',
            'email' => 'prueba@example.test',
            'password' => Hash::make('Password123!'),
            'estado' => $estado,
        ]);
    }

    private function seedReservationDomain(int $usuarioId): int
    {
        DB::table('departamentos')->insert(['id' => 1, 'nombre' => 'Lima']);
        DB::table('provincias')->insert(['id' => 1, 'id_departamento' => 1, 'nombre' => 'Lima']);
        DB::table('distritos')->insert(['id' => 1, 'id_provincia' => 1, 'nombre' => 'Distrito de prueba']);
        DB::table('complejo_deportivos')->insert([
            'id' => 1,
            'id_distrito' => 1,
            'nombre' => 'Complejo de prueba',
            'correo' => 'complejo@example.test',
            'telefono' => '999888777',
        ]);
        DB::table('tipo_canchas')->insert(['id' => 1, 'nombre' => 'Fútbol']);
        DB::table('canchas')->insert([
            'id' => 1,
            'id_complejo' => 1,
            'id_tipo_cancha' => 1,
            'nombre' => 'Cancha de prueba',
            'precio_hora' => 50,
        ]);
        $clienteId = DB::table('clientes')->insertGetId([
            'id_usuario' => $usuarioId,
            'documento_identidad' => 'DOC-PROPIETARIO',
        ]);
        DB::table('estado_reservas')->insert([
            ['id' => 1, 'nombre' => 'Confirmada'],
            ['id' => 2, 'nombre' => 'Completada'],
            ['id' => 3, 'nombre' => 'Cancelada'],
        ]);
        $reservaId = DB::table('reservas')->insertGetId([
            'codigo_reserva' => 'RES-DETALLE',
            'id_cliente' => $clienteId,
            'id_cancha' => 1,
            'id_estado_reserva' => 1,
            'fecha_reserva' => '2030-07-15',
            'hora_inicio' => '19:00',
            'hora_fin' => '20:00',
            'precio_hora' => 50,
            'subtotal' => 50,
            'total' => 50,
            'confirmado_at' => '2030-07-01 10:00:00',
        ]);
        DB::table('historial_estado_reservas')->insert([
            'id_reserva' => $reservaId,
            'id_estado_reserva' => 1,
            'id_usuario' => $usuarioId,
            'fecha_cambio' => '2030-07-01 10:00:00',
            'observacion' => 'Dato interno no visible',
        ]);

        return $reservaId;
    }
}
