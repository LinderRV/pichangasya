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
}
