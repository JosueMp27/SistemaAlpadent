<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function crearUsuario(): User
    {
        return User::create([
            'nombre' => 'Usuario',
            'apellido' => 'Prueba',
            'email' => 'prueba@alpadent.com',
            'password' => Hash::make('password123'),
        ]);
    }

    public function test_usuario_puede_iniciar_sesion(): void
    {
        $this->crearUsuario();

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'prueba@alpadent.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'usuario',
                'token',
                'token_type',
            ],
        ]);
    }

    public function test_usuario_no_puede_iniciar_sesion_con_password_incorrecto(): void
    {
        $this->crearUsuario();

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'prueba@alpadent.com',
            'password' => 'password_incorrecto',
        ]);

        $response->assertStatus(401);
    }

    public function test_usuario_autenticado_puede_obtener_su_perfil(): void
    {
        $usuario = $this->crearUsuario();

        $token = $usuario->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader(
            'Authorization',
            'Bearer ' . $token
        )->getJson('/api/v1/auth/me');

        $response->assertStatus(200);
    }

    public function test_usuario_puede_cerrar_sesion(): void
    {
        $usuario = $this->crearUsuario();

        $token = $usuario->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader(
            'Authorization',
            'Bearer ' . $token
        )->postJson('/api/v1/auth/logout');

        $response->assertStatus(200);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
// Pruebas funcionales de autenticacion ALPADENT.
