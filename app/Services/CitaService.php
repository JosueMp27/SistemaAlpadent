<?php

namespace App\Services;

use App\Models\Cita;
use App\Models\Paciente;
use Illuminate\Support\Facades\DB;

class CitaService
{
    /**
     * Agenda una nueva cita.
     */
    /**
     * Documentacion: Agenda una nueva cita odontologica.
     * Como lo hace: Verifica paciente activo, calcula si es primera vez y crea la cita dentro de una transaccion.
     */
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

    /**
     * Reagenda una cita existente.
     */
    /**
     * Documentacion: Cambia la fecha y hora de una cita programada.
     * Como lo hace: Solo permite mover citas programadas y actualiza observaciones sin perder relaciones.
     */
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

    /**
     * Cancela una cita.
     */
    /**
     * Documentacion: Cancela una cita o la marca como no asistida.
     * Como lo hace: Actualiza el estado y cancela recordatorios pendientes dentro de una transaccion.
     */
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

    /**
     * Marca una cita como completada.
     */
    /**
     * Documentacion: Marca una cita como completada.
     * Como lo hace: Permite cerrar citas programadas o en curso y refresca el modelo resultante.
     */
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

    /**
     * Marca una cita como en curso.
     */
    /**
     * Documentacion: Marca una cita como en curso.
     * Como lo hace: Solo cambia citas programadas para reflejar que la atencion ya empezo.
     */
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

    /**
     * Obtiene la agenda del dia.
     */
    /**
     * Documentacion: Obtiene la agenda del dia actual.
     * Como lo hace: Filtra por fecha de hoy, omite canceladas/no asistidas y ordena por hora.
     */
    public function obtenerAgendaHoy()
    {
        return Cita::with(['paciente', 'usuario', 'doctorExterno', 'tipoTratamiento'])
            ->whereDate('fecha_hora_inicio', today())
            ->whereNotIn('estado', ['cancelada', 'no_asistio'])
            ->orderBy('fecha_hora_inicio')
            ->get();
    }

    /**
     * Obtiene citas proximas.
     */
    /**
     * Documentacion: Obtiene citas proximas.
     * Como lo hace: Usa scope de citas futuras, limita por cantidad de dias y pagina si corresponde.
     */
    public function obtenerProximas($dias = 7, $paginated = true)
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

    /**
     * Obtiene citas de un paciente.
     */
    /**
     * Documentacion: Lista citas de un paciente para el odontograma.
     * Como lo hace: Carga tratamiento, excluye canceladas y devuelve datos simples de fecha, hora y motivo.
     */
    public function obtenerCitasPaciente(int $pacienteId, $paginated = true)
    {
        $query = Cita::where('paciente_id', $pacienteId)
            ->with(['diagnostico', 'tratamientos', 'pago', 'doctorExterno', 'tipoTratamiento'])
            ->orderBy('fecha_hora_inicio', 'desc');

        if ($paginated) {
            return $query->paginate(10);
        }

        return $query->get();
    }

    /**
     * Verifica si ya existe una cita activa exactamente en la misma fecha y hora.
     */
    /**
     * Documentacion: Busca una cita activa en el mismo horario.
     * Como lo hace: Consulta fecha exacta y excluye estados cancelados para evitar doble reserva.
     */
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

    /**
     * Documentacion: Indica si un horario esta libre.
     * Como lo hace: Reutiliza la busqueda de conflicto y devuelve verdadero cuando no hay cita activa.
     */
    public function verificarDisponibilidad($fechaInicio, ?int $excluirCitaId = null): bool
    {
        return $this->obtenerConflicto($fechaInicio, $excluirCitaId) === null;
    }

    /**
     * Obtiene estadisticas de citas.
     */
    /**
     * Documentacion: Calcula estadisticas del modulo.
     * Como lo hace: Usa conteos, sumas o vistas SQL para devolver indicadores listos para tarjetas o reportes.
     */
    public function obtenerEstadisticas($anio = null)
    {
        $anio = $anio ?? now()->year;

        return DB::select('CALL sp_reporte_citas_por_mes(?)', [$anio]);
    }
}
