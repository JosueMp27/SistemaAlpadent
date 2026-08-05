<?php

namespace Tests\Feature;

use App\Models\Paciente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PacienteTest extends TestCase
{
    use RefreshDatabase;

    protected function autenticar(): void
    {
        $usuario = User::create([
            'nombre' => 'Usuario',
            'apellido' => 'Prueba',
            'email' => 'prueba@alpadent.com',
            'password' => bcrypt('password123'),
        ]);

        Sanctum::actingAs($usuario);
    }

    public function test_usuario_autenticado_puede_listar_pacientes(): void
    {
        $this->autenticar();

        Paciente::create([
            'numero_historia' => 'HIST-001',
            'nombre' => 'Juan',
            'apellido' => 'Perez',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'M',
            'telefono' => '0999999999',
            'direccion' => 'El Guabo',
            'es_menor' => false,
            'activo' => true,
        ]);

        $response = $this->getJson('/api/v1/pacientes');

        $response->assertStatus(200);
    }

    public function test_usuario_autenticado_puede_crear_paciente(): void
    {
        $this->autenticar();

        $response = $this->postJson('/api/v1/pacientes', [
            'numero_historia' => 'HIST-002',
            'nombre' => 'Maria',
            'apellido' => 'Gomez',
            'fecha_nacimiento' => '1995-05-10',
            'sexo' => 'F',
            'telefono' => '0988888888',
            'direccion' => 'Machala',
            'es_menor' => false,
            'activo' => true,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('pacientes', [
            'nombre' => 'Maria',
            'apellido' => 'Gomez',
        ]);
    }

    public function test_usuario_autenticado_puede_obtener_paciente(): void
    {
        $this->autenticar();

        $paciente = Paciente::create([
            'numero_historia' => 'HIST-003',
            'nombre' => 'Pedro',
            'apellido' => 'Lopez',
            'fecha_nacimiento' => '1988-03-20',
            'sexo' => 'M',
            'telefono' => '0977777777',
            'direccion' => 'El Guabo',
            'es_menor' => false,
            'activo' => true,
        ]);

        $response = $this->getJson("/api/v1/pacientes/{$paciente->id}");

        $response->assertStatus(200);
    }

    public function test_usuario_autenticado_puede_actualizar_paciente(): void
    {
        $this->autenticar();

        $paciente = Paciente::create([
            'numero_historia' => 'HIST-004',
            'nombre' => 'Carlos',
            'apellido' => 'Mendoza',
            'fecha_nacimiento' => '1992-07-15',
            'sexo' => 'M',
            'telefono' => '0966666666',
            'direccion' => 'El Guabo',
            'es_menor' => false,
            'activo' => true,
        ]);

        $response = $this->putJson("/api/v1/pacientes/{$paciente->id}", [
            'numero_historia' => 'HIST-004',
            'nombre' => 'Carlos Actualizado',
            'apellido' => 'Mendoza',
            'fecha_nacimiento' => '1992-07-15',
            'sexo' => 'M',
            'telefono' => '0966666666',
            'direccion' => 'Machala',
            'es_menor' => false,
            'activo' => true,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('pacientes', [
            'id' => $paciente->id,
            'nombre' => 'Carlos Actualizado',
            'direccion' => 'Machala',
        ]);
    }

    public function test_usuario_autenticado_puede_desactivar_paciente(): void
    {
        $this->autenticar();

       $paciente = Paciente::create([
    'numero_historia' => 'HIST-005',
    'nombre' => 'Maria',
    'apellido' => 'Gomez',
    'fecha_nacimiento' => '1998-11-05',
    'sexo' => 'F',
    'telefono' => '0955555555',
    'direccion' => 'El Guabo',
    'es_menor' => false,
    'activo' => true,
]);

        $response = $this->deleteJson("/api/v1/pacientes/{$paciente->id}");

        $response->assertStatus(200);

        $this->assertDatabaseHas('pacientes', [
            'id' => $paciente->id,
            'activo' => false,
        ]);
    }

    public function test_usuario_no_autenticado_no_puede_acceder_a_pacientes(): void
    {
        $response = $this->getJson('/api/v1/pacientes');

        $response->assertStatus(401);
    }
}