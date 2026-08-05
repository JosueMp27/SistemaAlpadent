<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Diagnostico\Repositories\DiagnosticoRepositoryInterface;
use App\Models\Diagnostico;
use App\Models\Cita;
use App\Models\DienteDiagnostico;
use App\Models\Paciente;
use Illuminate\Support\Facades\DB;

class EloquentDiagnosticoRepository implements DiagnosticoRepositoryInterface
{
    public function registrar(array $datos): Diagnostico
    {
        $cita = Cita::where('id', $datos['cita_id'])
            ->whereIn('estado', ['programada', 'en_curso'])
            ->firstOrFail();

        if ($cita->diagnostico) {
            throw new \Exception('Esta cita ya tiene un diagnóstico registrado.');
        }

        return DB::transaction(function () use ($datos) {
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

    public function agregarDiente(int $diagnosticoId, array $datos): DienteDiagnostico
    {
        Diagnostico::findOrFail($diagnosticoId);

        return DB::transaction(function () use ($diagnosticoId, $datos) {
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

    public function actualizarDiente(int $dienteId, array $datos): DienteDiagnostico
    {
        $diente = DienteDiagnostico::findOrFail($dienteId);

        return DB::transaction(function () use ($diente, $datos) {
            $diente->update($datos);
            return $diente->fresh();
        });
    }

    public function eliminarDiente(int $dienteId): bool
    {
        $diente = DienteDiagnostico::findOrFail($dienteId);
        return $diente->delete();
    }

    public function obtenerPorCita(int $citaId)
    {
        return Diagnostico::where('cita_id', $citaId)
            ->with(['dientes', 'usuario', 'cita.paciente'])
            ->firstOrFail();
    }

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

    public function obtenerRecientes(int $limite = 10)
    {
        return Diagnostico::with(['cita.paciente', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->limit($limite)
            ->get();
    }

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
            $paciente = Paciente::findOrFail($pacienteId);
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
