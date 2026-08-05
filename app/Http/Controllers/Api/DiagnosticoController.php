<?php

/**
 * Documentacion de archivo:
 * Controlador API que recibe peticiones HTTP, valida entradas y delega en la capa de aplicacion Hexagonal.
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreDiagnosticoRequest;
use App\Application\Diagnostico\Services\DiagnosticoApplicationService;
use Illuminate\Http\Request;

class DiagnosticoController extends ApiController
{
    public function __construct(
        protected DiagnosticoApplicationService $diagnosticoService
    ) {}

    public function store(StoreDiagnosticoRequest $request)
    {
        try {
            $diagnostico = $this->diagnosticoService->registrar($request->validated());
            return $this->successResponse($diagnostico, 'Diagnóstico creado correctamente', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $diagnostico = $this->diagnosticoService->obtenerPorCita((int) $id);
            return $this->successResponse($diagnostico, 'Diagnóstico obtenido correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function agregarDiente(Request $request, $diagnosticoId)
    {
        try {
            $datos = $request->validate([
                'numero_diente' => 'required|integer',
                'condicion' => 'required|in:sano,cariado,obturado,faltante,con_tratamiento_radicular,con_corona,con_puente,implante,ausente',
                'superficie' => 'nullable|string',
                'observacion' => 'nullable|string',
            ]);

            $diente = $this->diagnosticoService->agregarDiente((int) $diagnosticoId, $datos);
            return $this->successResponse($diente, 'Diente agregado correctamente', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function actualizarDiente(Request $request, $denteDiagnosticoId)
    {
        try {
            $datos = $request->validate([
                'condicion' => 'required|in:sano,cariado,obturado,faltante,con_tratamiento_radicular,con_corona,con_puente,implante,ausente',
                'superficie' => 'nullable|string',
                'observacion' => 'nullable|string',
            ]);

            $diente = $this->diagnosticoService->actualizarDiente((int) $denteDiagnosticoId, $datos);
            return $this->successResponse($diente, 'Diente actualizado correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function eliminarDiente($denteDiagnosticoId)
    {
        try {
            $this->diagnosticoService->eliminarDiente((int) $denteDiagnosticoId);
            return $this->successResponse(null, 'Diente eliminado correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function odontograma($diagnosticoId)
    {
        try {
            $odontograma = $this->diagnosticoService->obtenerOdontograma((int) $diagnosticoId);
            return $this->successResponse($odontograma, 'Odontograma obtenido correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function recientes(Request $request)
    {
        try {
            $limite = (int) $request->query('limite', 10);
            $diagnosticos = $this->diagnosticoService->obtenerRecientes($limite);
            return $this->successResponse($diagnosticos, 'Diagnósticos recientes obtenidos');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function historialPaciente($pacienteId)
    {
        try {
            $historial = $this->diagnosticoService->obtenerHistorialPaciente((int) $pacienteId);
            return $this->successResponse($historial, 'Historial de diagnosticos obtenido correctamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Paciente no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function estadisticas()
    {
        try {
            $estadisticas = $this->diagnosticoService->obtenerEstadisticas();
            return $this->successResponse($estadisticas, 'Estadísticas obtenidas correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
