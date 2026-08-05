<?php

namespace App\Services;

use App\Models\Diagnostico;
use App\Models\Cita;
use App\Models\DienteDiagnostico;
use Illuminate\Support\Facades\DB;

/**
 * Documentacion de clase:
 * Servicio de negocio que concentra reglas, transacciones y consultas Eloquent para mantener limpios los controladores.
 */
class DiagnosticoService
{
    /**
     * Registra un diagnóstico completo con dientes afectados
     * Equivalente a sp_registrar_diagnostico
     */
    /**
     * Documentacion: Registra una entidad principal del modulo.
     * Como lo hace: Valida relaciones, aplica reglas de negocio y guarda los datos en una transaccion cuando hay cambios compuestos.
     */
    public function registrar(array $datos): Diagnostico
    {
        $cita = Cita::where('id', $datos['cita_id'])
            ->whereIn('estado', ['programada', 'en_curso'])
            ->firstOrFail();

        // Verificar que no tenga ya un diagnóstico
        if ($cita->diagnostico) {
            throw new \Exception('Esta cita ya tiene un diagnóstico registrado.');
        }

        return DB::transaction(function () use ($datos, $cita) {
            $diagnostico = Diagnostico::create([
                'cita_id' => $datos['cita_id'],
                'usuario_id' => $datos['usuario_id'],
                'descripcion' => $datos['descripcion'],
                'indice_cpo_cariados' => $datos['indice_cpo_cariados'] ?? 0,
                'indice_cpo_perdidos' => $datos['indice_cpo_perdidos'] ?? 0,
                'indice_cpo_obturados' => $datos['indice_cpo_obturados'] ?? 0,
                'gingivitis' => $datos['gingivitis'] ?? false,
                'enfermedad_periodontal' => $datos['enfermedad_periodontal'] ?? false,
            ]);

            return $diagnostico->fresh();
        });
    }

    /**
     * Agrega un diente afectado a un diagnóstico
     * Equivalente a sp_agregar_diente_diagnostico
     */
    /**
     * Documentacion: Agrega un diente al diagnostico.
     * Como lo hace: Crea una marca clinica asociada al diagnostico con condicion, superficie y observacion.
     */
    public function agregarDiente(int $diagnosticoId, array $datos): DienteDiagnostico
    {
        $diagnostico = Diagnostico::findOrFail($diagnosticoId);

        return DB::transaction(function () use ($diagnostico, $datos) {
            $diente = DienteDiagnostico::create([
                'diagnostico_id' => $diagnosticoId,
                'numero_diente' => $datos['numero_diente'],
                'condicion' => $datos['condicion'],
                'superficie' => $datos['superficie'] ?? null,
                'observacion' => $datos['observacion'] ?? null,
            ]);

            return $diente->fresh();
        });
    }

    /**
     * Actualiza un diente en el diagnóstico
     */
    /**
     * Documentacion: Actualiza la informacion de un diente diagnosticado.
     * Como lo hace: Busca el registro dental y reemplaza los campos enviados por el usuario.
     */
    public function actualizarDiente(int $dienteId, array $datos): DienteDiagnostico
    {
        $diente = DienteDiagnostico::findOrFail($dienteId);

        return DB::transaction(function () use ($diente, $datos) {
            $diente->update($datos);
            return $diente->fresh();
        });
    }

    /**
     * Elimina un diente del diagnóstico
     */
    /**
     * Documentacion: Elimina un diente del diagnostico.
     * Como lo hace: Borra la marca dental especifica para que ya no aparezca en el odontograma del diagnostico.
     */
    public function eliminarDiente(int $dienteId): bool
    {
        $diente = DienteDiagnostico::findOrFail($dienteId);
        return $diente->delete();
    }

    /**
     * Obtiene el diagnóstico de una cita con toda la información
     */
    /**
     * Documentacion: Genera un reporte solicitado.
     * Como lo hace: Valida el tipo, calcula filas y resumen, y agrega metadatos de usuario y fecha.
     */
    public function obtenerPorCita(int $citaId)
    {
        return Diagnostico::where('cita_id', $citaId)
            ->with(['dientes', 'usuario', 'cita.paciente'])
            ->firstOrFail();
    }

    /**
     * Obtiene el odontograma completo de un diagnóstico
     */
    /**
     * Documentacion: Construye el odontograma de un diagnostico.
     * Como lo hace: Agrupa dientes por numero y agrega resumen de indices CPO y hallazgos periodontales.
     */
    public function obtenerOdontograma(int $diagnosticoId)
    {
        $diagnostico = Diagnostico::with('dientes')->findOrFail($diagnosticoId);
        
        return [
            'diagnostico' => $diagnostico,
            'resumen' => [
                'indice_cpo' => $diagnostico->calcularIndiceCPO(),
                'tiene_gingivitis' => $diagnostico->gingivitis,
                'tiene_enfermedad_periodontal' => $diagnostico->enfermedad_periodontal,
            ],
            'dientes' => $diagnostico->dientes->groupBy('numero_diente'),
        ];
    }

    /**
     * Obtiene diagnósticos recientes
     */
    /**
     * Documentacion: Lista diagnosticos recientes.
     * Como lo hace: Ordena por fecha de creacion y limita la cantidad devuelta al frontend.
     */
    public function obtenerRecientes($limite = 10)
    {
        return Diagnostico::with(['cita.paciente', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->limit($limite)
            ->get();
    }

    /**
     * Obtiene estadísticas de diagnósticos
     */
    /**
     * Documentacion: Construye el historial clinico-financiero de un paciente.
     * Como lo hace: Recorre citas, diagnosticos, tratamientos y pagos para calcular costo, abono y saldo por visita.
     */
    public function obtenerHistorialPaciente(int $pacienteId): array
    {
        $citas = Cita::with([
                'paciente',
                'tipoTratamiento',
                'tratamientos.tipoTratamiento',
                'pago.abonos',
                'diagnostico',
            ])
            ->where('paciente_id', $pacienteId)
            ->orderBy('fecha_hora_inicio', 'desc')
            ->get();

        $paciente = $citas->first()?->paciente;

        if (! $paciente) {
            $paciente = \App\Models\Paciente::findOrFail($pacienteId);
        }

        $registros = $citas->map(function (Cita $cita) {
            $pago = $cita->pago;
            $abonosRegistrados = $pago ? (float) $pago->abonos->sum('monto') : 0.0;
            $abono = $pago ? max((float) $pago->monto_pagado, $abonosRegistrados) : 0.0;

            $costoTratamientos = (float) $cita->tratamientos->sum('precio_aplicado');
            $costoCatalogo = $cita->tipoTratamiento ? (float) $cita->tipoTratamiento->precio : 0.0;
            $costo = $pago ? (float) $pago->monto_total : ($costoTratamientos > 0 ? $costoTratamientos : $costoCatalogo);
            $saldo = $pago ? (float) $pago->saldo_pendiente : max($costo - $abono, 0);

            $tratamientos = $cita->tratamientos
                ->map(fn ($tratamiento) => $tratamiento->tipoTratamiento?->nombre)
                ->filter()
                ->values();

            if ($tratamientos->isEmpty() && $cita->tipoTratamiento) {
                $tratamientos->push($cita->tipoTratamiento->nombre);
            }

            return [
                'cita_id' => $cita->id,
                'fecha' => optional($cita->fecha_hora_inicio)->format('Y-m-d'),
                'hora' => optional($cita->fecha_hora_inicio)->format('H:i'),
                'tratamiento' => $tratamientos->isNotEmpty()
                    ? $tratamientos->implode(', ')
                    : ($cita->motivo_consulta ?? 'Consulta'),
                'diagnostico' => $cita->diagnostico?->descripcion,
                'estado' => $cita->estado,
                'costo' => round($costo, 2),
                'abono' => round($abono, 2),
                'saldo' => round($saldo, 2),
            ];
        })->values();

        return [
            'paciente' => $paciente,
            'registros' => $registros,
            'resumen' => [
                'total_citas' => $registros->count(),
                'total_costo' => round($registros->sum('costo'), 2),
                'total_abono' => round($registros->sum('abono'), 2),
                'total_saldo' => round($registros->sum('saldo'), 2),
            ],
        ];
    }

    /**
     * Documentacion: Calcula estadisticas del modulo.
     * Como lo hace: Usa conteos, sumas o vistas SQL para devolver indicadores listos para tarjetas o reportes.
     */
    public function obtenerEstadisticas()
    {
        return [
            'total_diagnosticos' => Diagnostico::count(),
            'con_gingivitis' => Diagnostico::where('gingivitis', true)->count(),
            'con_enfermedad_periodontal' => Diagnostico::where('enfermedad_periodontal', true)->count(),
            'promedio_indice_cpo' => Diagnostico::selectRaw(
                '(SUM(indice_cpo_cariados) + SUM(indice_cpo_perdidos) + SUM(indice_cpo_obturados)) / COUNT(*) as promedio'
            )->value('promedio') ?? 0,
        ];
    }
}
