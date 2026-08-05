<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

    protected function crearUsuario(): User
    {
        return User::create([
            'nombre' => 'Usuario',
            'apellido' => 'Errores',
            'email' => 'errores@alpadent.com',
            'password' => bcrypt('password123'),
        ]);
    }

    public function test_no_se_puede_obtener_un_paciente_inexistente(): void
    {
        $usuario = $this->crearUsuario();

        Sanctum::actingAs($usuario);

        $response = $this->getJson('/api/v1/pacientes/99999');

        $response->assertStatus(404);
    }

    public function test_no_se_puede_crear_paciente_con_datos_invalidos(): void
    {
        $usuario = $this->crearUsuario();

        Sanctum::actingAs($usuario);

        $response = $this->postJson('/api/v1/pacientes', [
            'nombre' => '',
            'apellido' => '',
            'fecha_nacimiento' => 'fecha-invalida',
        ]);

        $response->assertStatus(422);
    }

    public function test_no_se_puede_obtener_una_cita_inexistente(): void
    {
        $usuario = $this->crearUsuario();

        Sanctum::actingAs($usuario);

        $response = $this->getJson('/api/v1/citas/99999');

        $response->assertStatus(404);
    }

    public function test_no_se_puede_obtener_un_pago_inexistente(): void
    {
        $usuario = $this->crearUsuario();

        Sanctum::actingAs($usuario);

        $response = $this->getJson('/api/v1/pagos/99999');

        $response->assertStatus(404);
    }
}