<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\TipoTratamiento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CitaTest extends TestCase
{
    use RefreshDatabase;

    protected function autenticar(): User
    {
        $usuario = User::create([
            'nombre' => 'Usuario',
            'apellido' => 'Prueba',
            'email' => 'prueba@alpadent.com',
            'password' => bcrypt('password123'),
        ]);

        Sanctum::actingAs($usuario);

        return $usuario;
    }

    protected function crearPaciente(): Paciente
    {
        return Paciente::create([
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
    }

    protected function crearTratamiento(): TipoTratamiento
    {
        return TipoTratamiento::create([
            'nombre' => 'Limpieza dental',
            'categoria' => 'limpieza',
            'precio' => 30.00,
            'descripcion' => 'Limpieza dental general',
            'activo' => true,
        ]);
    }

    protected function datosCita(
        User $usuario,
        Paciente $paciente,
        TipoTratamiento $tratamiento,
        string $fecha = '2030-06-15 10:00:00'
    ): array {
        return [
            'paciente_id' => $paciente->id,
            'usuario_id' => $usuario->id,
            'tipo_tratamiento_id' => $tratamiento->id,
            'fecha_hora_inicio' => $fecha,
            'motivo_consulta' => 'Dolor dental',
            'observaciones' => 'Paciente presenta molestias',
        ];
    }

    public function test_usuario_autenticado_puede_listar_citas(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $tratamiento = $this->crearTratamiento();

        Cita::create($this->datosCita(
            $usuario,
            $paciente,
            $tratamiento
        ));

        $response = $this->getJson('/api/v1/citas');

        $response->assertStatus(200);
    }

    public function test_usuario_autenticado_puede_crear_cita(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $tratamiento = $this->crearTratamiento();

        $datos = $this->datosCita(
            $usuario,
            $paciente,
            $tratamiento,
            '2030-06-16 10:00:00'
        );

        $response = $this->postJson('/api/v1/citas', $datos);

        $response->assertStatus(201);

        $this->assertDatabaseHas('citas', [
            'paciente_id' => $paciente->id,
            'usuario_id' => $usuario->id,
            'tipo_tratamiento_id' => $tratamiento->id,
            'motivo_consulta' => 'Dolor dental',
            'estado' => 'programada',
        ]);
    }

    public function test_usuario_autenticado_puede_obtener_cita(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $tratamiento = $this->crearTratamiento();

        $cita = Cita::create($this->datosCita(
            $usuario,
            $paciente,
            $tratamiento,
            '2030-06-17 11:00:00'
        ));

        $response = $this->getJson("/api/v1/citas/{$cita->id}");

        $response->assertStatus(200);
    }

    public function test_usuario_autenticado_puede_reagendar_cita(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $tratamiento = $this->crearTratamiento();

        $cita = Cita::create($this->datosCita(
            $usuario,
            $paciente,
            $tratamiento,
            '2030-06-18 10:00:00'
        ));

        $response = $this->putJson("/api/v1/citas/{$cita->id}", [
            'fecha_hora_inicio' => '2030-06-18 15:00:00',
            'observaciones' => 'Cita reagendada',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('citas', [
            'id' => $cita->id,
            'observaciones' => 'Cita reagendada',
        ]);
    }

    public function test_usuario_autenticado_puede_cancelar_cita(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $tratamiento = $this->crearTratamiento();

        $cita = Cita::create($this->datosCita(
            $usuario,
            $paciente,
            $tratamiento,
            '2030-06-19 10:00:00'
        ));

        $response = $this->postJson("/api/v1/citas/{$cita->id}/cancelar", [
            'estado' => 'cancelada',
            'observaciones' => 'Paciente canceló la cita',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('citas', [
            'id' => $cita->id,
            'estado' => 'cancelada',
            'observaciones' => 'Paciente canceló la cita',
        ]);
    }

    public function test_usuario_autenticado_puede_iniciar_cita(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $tratamiento = $this->crearTratamiento();

        $cita = Cita::create($this->datosCita(
            $usuario,
            $paciente,
            $tratamiento,
            '2030-06-20 10:00:00'
        ));

        $response = $this->postJson("/api/v1/citas/{$cita->id}/iniciar");

        $response->assertStatus(200);

        $this->assertDatabaseHas('citas', [
            'id' => $cita->id,
            'estado' => 'en_curso',
        ]);
    }

    public function test_usuario_autenticado_puede_completar_cita(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $tratamiento = $this->crearTratamiento();

        $cita = Cita::create($this->datosCita(
            $usuario,
            $paciente,
            $tratamiento,
            '2030-06-21 10:00:00'
        ));

        $response = $this->postJson("/api/v1/citas/{$cita->id}/completar");

        $response->assertStatus(200);

        $this->assertDatabaseHas('citas', [
            'id' => $cita->id,
            'estado' => 'completada',
        ]);
    }

    public function test_no_se_puede_crear_cita_en_horario_ocupado(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $tratamiento = $this->crearTratamiento();

        $fecha = '2030-06-22 10:00:00';

        Cita::create($this->datosCita(
            $usuario,
            $paciente,
            $tratamiento,
            $fecha
        ));

        $response = $this->postJson('/api/v1/citas', [
            'paciente_id' => $paciente->id,
            'usuario_id' => $usuario->id,
            'tipo_tratamiento_id' => $tratamiento->id,
            'fecha_hora_inicio' => $fecha,
            'motivo_consulta' => 'Otra consulta',
            'observaciones' => null,
        ]);

        $response->assertStatus(409);

        $response->assertJson([
            'success' => false,
            'message' => 'No hay disponibilidad en ese horario',
        ]);
    }

    public function test_usuario_no_autenticado_no_puede_acceder_a_citas(): void
    {
        $response = $this->getJson('/api/v1/citas');

        $response->assertStatus(401);
    }
}
// Pruebas funcionales del modulo de citas ALPADENT.
