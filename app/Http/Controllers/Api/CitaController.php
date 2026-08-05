<?php

/**
 * Documentacion de archivo:
 * Controlador API que recibe peticiones HTTP, valida entradas y delega en la capa de aplicacion Hexagonal.
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreCitaRequest;
use App\Application\Cita\Services\CitaApplicationService;
use App\Models\Cita;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CitaController extends ApiController
{
    public function __construct(
        protected CitaApplicationService $citaService
    ) {}

    public function index(Request $request)
    {
        try {
            $estado = $request->query('estado');
            $pacienteId = $request->query('paciente_id');

            $query = Cita::with(['paciente', 'usuario', 'doctorExterno', 'diagnostico', 'tratamientos', 'tipoTratamiento'])
                ->orderBy('fecha_hora_inicio', 'desc');

            if ($estado) {
                $query->where('estado', $estado);
            }

            if ($pacienteId) {
                $query->where('paciente_id', $pacienteId);
            }

            $citas = $query->paginate(15);

            return $this->successResponse($citas, 'Citas obtenidas correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function store(StoreCitaRequest $request)
    {
        try {
            $conflicto = $this->citaService->obtenerConflicto($request->fecha_hora_inicio);

            if ($conflicto) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay disponibilidad en ese horario',
                    'conflicto' => $this->formatearConflicto($conflicto),
                ], 409);
            }

            $cita = $this->citaService->agendar($request->validated());

            return $this->successResponse($cita, 'Cita registrada correctamente', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $cita = $this->citaService->obtenerPorId((int) $id);
            return $this->successResponse($cita, 'Cita obtenida correctamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Cita no encontrada');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $datos = $request->validate([
                'fecha_hora_inicio' => 'required|date|after:now',
                'observaciones' => 'nullable|string',
            ]);

            $conflicto = $this->citaService->obtenerConflicto($datos['fecha_hora_inicio'], (int) $id);

            if ($conflicto) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay disponibilidad en ese horario',
                    'conflicto' => $this->formatearConflicto($conflicto),
                ], 409);
            }

            $cita = $this->citaService->reagendar((int) $id, $datos);

            return $this->successResponse($cita, 'Cita reagendada correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function cancelar(Request $request, $id)
    {
        try {
            $datos = $request->validate([
                'estado' => 'required|in:cancelada,no_asistio',
                'observaciones' => 'nullable|string',
            ]);

            $cita = $this->citaService->cancelar((int) $id, $datos['estado'], $datos['observaciones'] ?? null);

            return $this->successResponse($cita, 'Cita cancelada correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function completar($id)
    {
        try {
            $cita = $this->citaService->completar((int) $id);
            return $this->successResponse($cita, 'Cita completada correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function iniciar($id)
    {
        try {
            $cita = $this->citaService->iniciar((int) $id);
            return $this->successResponse($cita, 'Cita iniciada correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function agendaHoy()
    {
        try {
            $agenda = $this->citaService->obtenerAgendaHoy();
            return $this->successResponse($agenda, 'Agenda del dia obtenida');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function disponibilidadDia(Request $request)
    {
        try {
            $fecha = $request->query('fecha');

            if (! $fecha) {
                return $this->errorResponse('La fecha es requerida', 400);
            }

            $citas = Cita::with(['paciente', 'tipoTratamiento', 'doctorExterno'])
                ->whereDate('fecha_hora_inicio', $fecha)
                ->whereNotIn('estado', ['cancelada', 'no_asistio'])
                ->orderBy('fecha_hora_inicio', 'asc')
                ->get();

            return $this->successResponse($citas, 'Disponibilidad obtenida correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function proximas(Request $request)
    {
        try {
            $dias = (int) $request->query('dias', 7);
            $citas = $this->citaService->obtenerProximas($dias);

            return $this->successResponse($citas, 'Citas proximas obtenidas');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    private function formatearConflicto(Cita $conflicto): array
    {
        return [
            'paciente' => $conflicto->paciente
                ? $conflicto->paciente->nombre . ' ' . $conflicto->paciente->apellido
                : 'Paciente no identificado',
            'inicio' => Carbon::parse($conflicto->fecha_hora_inicio)->format('H:i'),
        ];
    }
}
