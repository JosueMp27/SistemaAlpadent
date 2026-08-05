<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function crearUsuario(): User
    {
        return User::create([
            'nombre' => 'Usuario',
            'apellido' => 'Seguridad',
            'email' => 'seguridad@alpadent.com',
            'password' => bcrypt('password123'),
        ]);
    }

    public function test_usuario_no_autenticado_no_puede_acceder_a_recursos_protegidos(): void
    {
        $response = $this->getJson('/api/v1/pacientes');

        $response->assertStatus(401);
    }

    public function test_usuario_autenticado_puede_acceder_a_recursos_protegidos(): void
    {
        $usuario = $this->crearUsuario();

        Sanctum::actingAs($usuario);

        $response = $this->getJson('/api/v1/pacientes');

        $response->assertStatus(200);
    }

    public function test_login_rechaza_credenciales_incorrectas(): void
    {
        $this->crearUsuario();

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'seguridad@alpadent.com',
            'password' => 'password_incorrecto',
        ]);

        $response->assertStatus(401);
    }

    public function test_usuario_autenticado_puede_cerrar_su_sesion(): void
    {
        $usuario = $this->crearUsuario();

        $token = $usuario->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader(
            'Authorization',
            'Bearer ' . $token
        )->postJson('/api/v1/auth/logout');

        $response->assertStatus(200);
    }
}