<?php

namespace Tests\Feature;

use App\Models\Abono;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Pago;
use App\Models\TipoTratamiento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PagoTest extends TestCase
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

    protected function crearCita(
        User $usuario,
        Paciente $paciente
    ): Cita {
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

    protected function crearPago(
        User $usuario,
        Paciente $paciente,
        Cita $cita,
        float $montoTotal = 30,
        float $montoPagado = 0
    ): Pago {
        return Pago::create([
            'paciente_id' => $paciente->id,
            'cita_id' => $cita->id,
            'usuario_id' => $usuario->id,
            'monto_total' => $montoTotal,
            'monto_pagado' => $montoPagado,
            'saldo_pendiente' => $montoTotal - $montoPagado,
            'estado' => $montoPagado >= $montoTotal
                ? 'pagado'
                : ($montoPagado > 0 ? 'parcial' : 'pendiente'),
            'metodo_pago' => 'efectivo',
            'referencia_transferencia' => null,
        ]);
    }

    public function test_usuario_autenticado_puede_crear_pago(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $cita = $this->crearCita($usuario, $paciente);

        $response = $this->postJson('/api/v1/pagos', [
            'paciente_id' => $paciente->id,
            'cita_id' => $cita->id,
            'usuario_id' => $usuario->id,
            'monto_total' => 30,
            'monto_pagado' => 10,
            'metodo_pago' => 'efectivo',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('pagos', [
            'paciente_id' => $paciente->id,
            'cita_id' => $cita->id,
            'monto_total' => 30,
            'monto_pagado' => 10,
            'saldo_pendiente' => 20,
            'estado' => 'parcial',
        ]);
    }

    public function test_usuario_autenticado_puede_obtener_lista_de_pagos(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $cita = $this->crearCita($usuario, $paciente);

        $this->crearPago($usuario, $paciente, $cita);

        $response = $this->getJson('/api/v1/pagos');

        $response->assertStatus(200);
    }

    public function test_usuario_autenticado_puede_obtener_detalle_de_pago(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $cita = $this->crearCita($usuario, $paciente);

        $pago = $this->crearPago($usuario, $paciente, $cita);

        $response = $this->getJson(
            "/api/v1/pagos/{$pago->id}"
        );

        $response->assertStatus(200);
    }

    public function test_usuario_autenticado_puede_registrar_abono(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $cita = $this->crearCita($usuario, $paciente);

        $pago = $this->crearPago(
            $usuario,
            $paciente,
            $cita,
            30,
            0
        );

        $response = $this->postJson(
            '/api/v1/pagos/abono/registrar',
            [
                'pago_id' => $pago->id,
                'usuario_id' => $usuario->id,
                'monto' => 10,
                'metodo_pago' => 'efectivo',
            ]
        );

        $response->assertStatus(201);

        $this->assertDatabaseHas('abonos', [
            'pago_id' => $pago->id,
            'usuario_id' => $usuario->id,
            'monto' => 10,
            'metodo_pago' => 'efectivo',
        ]);

        $this->assertDatabaseHas('pagos', [
            'id' => $pago->id,
            'monto_pagado' => 10,
            'saldo_pendiente' => 20,
            'estado' => 'parcial',
        ]);
    }

    public function test_usuario_autenticado_puede_obtener_pagos_pendientes_de_un_paciente(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $cita = $this->crearCita($usuario, $paciente);

        $this->crearPago($usuario, $paciente, $cita);

        $response = $this->getJson(
            "/api/v1/pagos/paciente/{$paciente->id}/pendientes"
        );

        $response->assertStatus(200);
    }

    public function test_usuario_autenticado_puede_obtener_historial_de_pagos(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $cita = $this->crearCita($usuario, $paciente);

        $this->crearPago($usuario, $paciente, $cita);

        $response = $this->getJson(
            "/api/v1/pagos/paciente/{$paciente->id}/historial"
        );

        $response->assertStatus(200);
    }

    public function test_usuario_autenticado_puede_obtener_saldo_de_un_paciente(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $cita = $this->crearCita($usuario, $paciente);

        $this->crearPago(
            $usuario,
            $paciente,
            $cita,
            30,
            10
        );

        $response = $this->getJson(
            "/api/v1/pagos/paciente/{$paciente->id}/saldo"
        );

        $response->assertStatus(200);
    }

    public function test_usuario_autenticado_puede_obtener_citas_para_cobro(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();

        $this->crearCita($usuario, $paciente);

        $response = $this->getJson(
            '/api/v1/pagos/citas'
        );

        $response->assertStatus(200);
    }

    public function test_usuario_autenticado_puede_obtener_detalle_de_cita_para_pago(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $cita = $this->crearCita($usuario, $paciente);

        $response = $this->getJson(
            "/api/v1/pagos/cita/{$cita->id}"
        );

        $response->assertStatus(200);
    }

    public function test_usuario_autenticado_puede_cobrar_una_cita(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $cita = $this->crearCita($usuario, $paciente);

        $response = $this->postJson(
            "/api/v1/pagos/cita/{$cita->id}/cobrar",
            [
                'usuario_id' => $usuario->id,
                'monto' => 30,
                'metodo_pago' => 'efectivo',
            ]
        );

        $response->assertStatus(201);

        $this->assertDatabaseHas('pagos', [
            'cita_id' => $cita->id,
            'monto_total' => 30,
            'monto_pagado' => 30,
            'saldo_pendiente' => 0,
            'estado' => 'pagado',
        ]);

        $this->assertDatabaseHas('abonos', [
            'monto' => 30,
            'metodo_pago' => 'efectivo',
        ]);
    }

    public function test_usuario_autenticado_puede_obtener_estadisticas_de_pagos(): void
    {
        $usuario = $this->autenticar();

        $response = $this->getJson(
            '/api/v1/pagos/reportes/estadisticas'
        );

        $response->assertStatus(200);
    }

    public function test_usuario_autenticado_puede_obtener_metodos_de_pago(): void
    {
        $usuario = $this->autenticar();

        $response = $this->getJson(
            '/api/v1/pagos/reportes/metodos'
        );

        $response->assertStatus(200);
    }

    public function test_usuario_no_autenticado_no_puede_acceder_a_pagos(): void
    {
        $response = $this->getJson(
            '/api/v1/pagos'
        );

        $response->assertStatus(401);
    }

    public function test_no_se_puede_crear_dos_pagos_para_la_misma_cita(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $cita = $this->crearCita($usuario, $paciente);

        $this->crearPago($usuario, $paciente, $cita);

        $response = $this->postJson('/api/v1/pagos', [
            'paciente_id' => $paciente->id,
            'cita_id' => $cita->id,
            'usuario_id' => $usuario->id,
            'monto_total' => 30,
            'monto_pagado' => 0,
            'metodo_pago' => 'efectivo',
        ]);

        $response->assertStatus(500);
    }

    public function test_no_se_puede_registrar_abono_en_pago_completado(): void
    {
        $usuario = $this->autenticar();
        $paciente = $this->crearPaciente();
        $cita = $this->crearCita($usuario, $paciente);

        $pago = $this->crearPago(
            $usuario,
            $paciente,
            $cita,
            30,
            30
        );

        $response = $this->postJson(
            '/api/v1/pagos/abono/registrar',
            [
                'pago_id' => $pago->id,
                'usuario_id' => $usuario->id,
                'monto' => 5,
                'metodo_pago' => 'efectivo',
            ]
        );

        $response->assertStatus(500);
    }
}
// Pruebas funcionales del modulo de pagos ALPADENT.
