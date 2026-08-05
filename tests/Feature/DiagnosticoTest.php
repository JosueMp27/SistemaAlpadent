<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Diagnostico;
use App\Models\DienteDiagnostico;
use App\Models\Paciente;
use App\Models\TipoTratamiento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DiagnosticoTest extends TestCase
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

    protected function crearCita(User $usuario, Paciente $paciente): Cita
    {
        $tipoTratamiento = TipoTratamiento::create([
            'nombre' => 'Limpieza dental',
            'categoria' => 'limpieza',
            'precio' => 30,
            'descripcion' => 'Limpieza dental general',
            'activo' => true,
        ]);

        return Cita::create([
            'paciente_id' => $paciente->id,
            'usuario_id' => $usuario->id,
            'tipo_tratamiento_id' => $tipoTratamiento->id,
            'fecha_hora_inicio' => now()->addDay()->setTime(10, 0),
            'motivo_consulta' => 'Dolor dental',
            'estado' => 'programada',
            'es_primera_vez' => true,
        ]);
    }

    protected function crearDiagnostico(
        User $usuario,
        Cita $cita
    ): Diagnostico {
        return Diagnostico::create([
            'cita_id' => $cita->id,
            'usuario_id' => $usuario->id,
            'descripcion' => 'Caries dental en pieza molar',
            'indice_cpo_cariados' => 2,
            'indice_cpo_perdidos' => 1,
            'indice_cpo_obturados' => 3,
            'gingivitis' => true,
            'enfermedad_periodontal' => false,
        ]);
    }

    public function test_usuario_autenticado_puede_crear_diagnostico(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $cita = $this->crearCita($usuario, $paciente);

        $response = $this->postJson('/api/v1/diagnosticos', [
            'cita_id' => $cita->id,
            'usuario_id' => $usuario->id,
            'descripcion' => 'Caries dental en pieza molar',
            'indice_cpo_cariados' => 2,
            'indice_cpo_perdidos' => 1,
            'indice_cpo_obturados' => 3,
            'gingivitis' => true,
            'enfermedad_periodontal' => false,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('diagnosticos', [
            'cita_id' => $cita->id,
            'usuario_id' => $usuario->id,
            'descripcion' => 'Caries dental en pieza molar',
        ]);
    }

    public function test_usuario_autenticado_puede_obtener_diagnostico_por_cita(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $cita = $this->crearCita($usuario, $paciente);

        $diagnostico = $this->crearDiagnostico($usuario, $cita);

        $response = $this->getJson(
            "/api/v1/diagnosticos/cita/{$cita->id}"
        );

        $response->assertStatus(200);
    }

    public function test_usuario_autenticado_puede_agregar_diente_al_diagnostico(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $cita = $this->crearCita($usuario, $paciente);

        $diagnostico = $this->crearDiagnostico($usuario, $cita);

        $response = $this->postJson(
            "/api/v1/diagnosticos/{$diagnostico->id}/diente",
            [
                'numero_diente' => 16,
                'condicion' => 'cariado',
                'superficie' => 'oclusal',
                'observacion' => 'Caries profunda',
            ]
        );

        $response->assertStatus(201);

        $this->assertDatabaseHas('dientes_diagnostico', [
            'diagnostico_id' => $diagnostico->id,
            'numero_diente' => 16,
            'condicion' => 'cariado',
        ]);
    }

    public function test_usuario_autenticado_puede_actualizar_diente(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $cita = $this->crearCita($usuario, $paciente);

        $diagnostico = $this->crearDiagnostico($usuario, $cita);

        $diente = DienteDiagnostico::create([
            'diagnostico_id' => $diagnostico->id,
            'numero_diente' => 16,
            'condicion' => 'cariado',
            'superficie' => 'oclusal',
            'observacion' => 'Caries inicial',
        ]);

        $response = $this->putJson(
            "/api/v1/diagnosticos/diente/{$diente->id}",
            [
                'condicion' => 'obturado',
                'superficie' => 'oclusal',
                'observacion' => 'Restauración realizada',
            ]
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('dientes_diagnostico', [
            'id' => $diente->id,
            'condicion' => 'obturado',
            'observacion' => 'Restauración realizada',
        ]);
    }

    public function test_usuario_autenticado_puede_eliminar_diente(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $cita = $this->crearCita($usuario, $paciente);

        $diagnostico = $this->crearDiagnostico($usuario, $cita);

        $diente = DienteDiagnostico::create([
            'diagnostico_id' => $diagnostico->id,
            'numero_diente' => 16,
            'condicion' => 'cariado',
            'superficie' => 'oclusal',
            'observacion' => 'Caries inicial',
        ]);

        $response = $this->deleteJson(
            "/api/v1/diagnosticos/diente/{$diente->id}"
        );

        $response->assertStatus(200);

        $this->assertDatabaseMissing('dientes_diagnostico', [
            'id' => $diente->id,
        ]);
    }

    public function test_usuario_autenticado_puede_obtener_odontograma(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $cita = $this->crearCita($usuario, $paciente);

        $diagnostico = $this->crearDiagnostico($usuario, $cita);

        DienteDiagnostico::create([
            'diagnostico_id' => $diagnostico->id,
            'numero_diente' => 16,
            'condicion' => 'cariado',
            'superficie' => 'oclusal',
            'observacion' => 'Caries',
        ]);

        $response = $this->getJson(
            "/api/v1/diagnosticos/{$diagnostico->id}/odontograma"
        );

        $response->assertStatus(200);
    }

    public function test_usuario_autenticado_puede_obtener_diagnosticos_recientes(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $cita = $this->crearCita($usuario, $paciente);

        $this->crearDiagnostico($usuario, $cita);

        $response = $this->getJson(
            '/api/v1/diagnosticos/listado/recientes'
        );

        $response->assertStatus(200);
    }

    public function test_usuario_autenticado_puede_obtener_historial_de_diagnosticos(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $cita = $this->crearCita($usuario, $paciente);

        $this->crearDiagnostico($usuario, $cita);

        $response = $this->getJson(
            "/api/v1/diagnosticos/paciente/{$paciente->id}/historial"
        );

        $response->assertStatus(200);
    }

    public function test_usuario_autenticado_puede_obtener_estadisticas(): void
    {
        $usuario = $this->autenticar();

        $response = $this->getJson(
            '/api/v1/diagnosticos/reportes/estadisticas'
        );

        $response->assertStatus(200);
    }

    public function test_usuario_no_autenticado_no_puede_acceder_a_diagnosticos(): void
    {
        $response = $this->getJson(
            '/api/v1/diagnosticos/listado/recientes'
        );

        $response->assertStatus(401);
    }
}
// Pruebas funcionales del modulo de diagnosticos ALPADENT.
