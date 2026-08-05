<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Cita\Repositories\CitaRepositoryInterface;
use App\Models\Cita;
use App\Models\Paciente;
use Illuminate\Support\Facades\DB;

class EloquentCitaRepository implements CitaRepositoryInterface
{
    public function agendar(array $datos): Cita
    {
        Paciente::where('id', $datos['paciente_id'])
            ->where('activo', true)
            ->firstOrFail();

        return DB::transaction(function () use ($datos) {
            $esPrimeraVez = ! Cita::where('paciente_id', $datos['paciente_id'])
                ->whereIn('estado', ['completada', 'en_curso'])
                ->exists();

            $cita = Cita::create([
                'paciente_id' => $datos['paciente_id'],
                'usuario_id' => $datos['usuario_id'],
                'tipo_tratamiento_id' => $datos['tipo_tratamiento_id'],
                'doctor_externo_id' => $datos['doctor_externo_id'] ?? null,
                'fecha_hora_inicio' => $datos['fecha_hora_inicio'],
                'motivo_consulta' => $datos['motivo_consulta'],
                'observaciones' => $datos['observaciones'] ?? null,
                'es_primera_vez' => $esPrimeraVez,
            ]);

            return $cita->fresh()->load('paciente', 'usuario', 'doctorExterno', 'recordatorio', 'tipoTratamiento');
        });
    }

    public function reagendar(int $citaId, array $datos): Cita
    {
        $cita = Cita::where('id', $citaId)
            ->where('estado', 'programada')
            ->firstOrFail();

        return DB::transaction(function () use ($cita, $datos) {
            $cita->update([
                'fecha_hora_inicio' => $datos['fecha_hora_inicio'],
                'observaciones' => $datos['observaciones'] ?? $cita->observaciones,
            ]);

            return $cita->fresh()->load('paciente', 'usuario', 'doctorExterno', 'recordatorio', 'tipoTratamiento');
        });
    }

    public function cancelar(int $citaId, string $estado, ?string $observaciones = null): Cita
    {
        $cita = Cita::where('id', $citaId)
            ->where('estado', 'programada')
            ->firstOrFail();

        return DB::transaction(function () use ($cita, $estado, $observaciones) {
            $cita->update([
                'estado' => $estado,
                'observaciones' => $observaciones ?? $cita->observaciones,
            ]);

            $recordatorio = $cita->recordatorio;
            if ($recordatorio && $recordatorio->estaPendiente()) {
                $recordatorio->update(['estado' => 'cancelado']);
            }

            return $cita->fresh();
        });
    }

    public function completar(int $citaId): Cita
    {
        $cita = Cita::where('id', $citaId)
            ->whereIn('estado', ['programada', 'en_curso'])
            ->firstOrFail();

        return DB::transaction(function () use ($cita) {
            $cita->update(['estado' => 'completada']);

            return $cita->fresh();
        });
    }

    public function iniciar(int $citaId): Cita
    {
        $cita = Cita::where('id', $citaId)
            ->where('estado', 'programada')
            ->firstOrFail();

        return DB::transaction(function () use ($cita) {
            $cita->update(['estado' => 'en_curso']);

            return $cita->fresh();
        });
    }

    public function obtenerAgendaHoy()
    {
        return Cita::with(['paciente', 'usuario', 'doctorExterno', 'tipoTratamiento'])
            ->whereDate('fecha_hora_inicio', today())
            ->whereNotIn('estado', ['cancelada', 'no_asistio'])
            ->orderBy('fecha_hora_inicio')
            ->get();
    }

    public function obtenerProximas(int $dias = 7, bool $paginated = true)
    {
        $query = Cita::proximamente()
            ->where('fecha_hora_inicio', '<=', now()->addDays($dias))
            ->with(['paciente', 'usuario', 'doctorExterno', 'tipoTratamiento'])
            ->orderBy('fecha_hora_inicio', 'asc');

        if ($paginated) {
            return $query->paginate(15);
        }

        return $query->get();
    }

    public function obtenerCitasPaciente(int $pacienteId, bool $paginated = true)
    {
        $query = Cita::where('paciente_id', $pacienteId)
            ->with(['diagnostico', 'tratamientos', 'pago', 'doctorExterno', 'tipoTratamiento'])
            ->orderBy('fecha_hora_inicio', 'desc');

        if ($paginated) {
            return $query->paginate(10);
        }

        return $query->get();
    }

    public function obtenerConflicto($fechaInicio, ?int $excluirCitaId = null): ?Cita
    {
        $query = Cita::with('paciente')
            ->where('fecha_hora_inicio', $fechaInicio)
            ->whereNotIn('estado', ['cancelada', 'no_asistio']);

        if ($excluirCitaId) {
            $query->where('id', '!=', $excluirCitaId);
        }

        return $query->first();
    }

    public function verificarDisponibilidad($fechaInicio, ?int $excluirCitaId = null): bool
    {
        return $this->obtenerConflicto($fechaInicio, $excluirCitaId) === null;
    }

    public function obtenerEstadisticas(?int $anio = null)
    {
        $anio = $anio ?? now()->year;

        return DB::select('CALL sp_reporte_citas_por_mes(?)', [$anio]);
    }

    public function obtenerPorId(int $citaId): Cita
    {
        return Cita::with(['paciente', 'usuario', 'doctorExterno', 'tipoTratamiento', 'recordatorio'])->findOrFail($citaId);
    }
}
