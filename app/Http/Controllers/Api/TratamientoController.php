<?php

/**
 * Documentacion de archivo:
 * Controlador API que recibe peticiones HTTP, valida entradas y delega en la capa de aplicacion Hexagonal.
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreTratamientoRequest;
use App\Application\Tratamiento\Services\TratamientoApplicationService;
use App\Models\TratamientoRealizado;
use Illuminate\Http\Request;

class TratamientoController extends ApiController
{
    public function __construct(
        protected TratamientoApplicationService $tratamientoService
    ) {}

    public function store(StoreTratamientoRequest $request)
    {
        try {
            $tratamiento = $this->tratamientoService->registrar($request->validated());
            return $this->successResponse($tratamiento, 'Tratamiento registrado correctamente', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $tratamiento = TratamientoRealizado::with(['tipoTratamiento', 'cita', 'diagnostico'])
                ->findOrFail($id);
            return $this->successResponse($tratamiento, 'Tratamiento obtenido correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $datos = $request->validate([
                'precio_aplicado' => 'nullable|numeric|min:0',
                'notas' => 'nullable|string',
                'numero_diente' => 'nullable|integer',
            ]);

            $tratamiento = $this->tratamientoService->actualizar((int) $id, $datos);
            return $this->successResponse($tratamiento, 'Tratamiento actualizado correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->tratamientoService->eliminar((int) $id);
            return $this->successResponse(null, 'Tratamiento eliminado correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function porCita($citaId)
    {
        try {
            $tratamientos = $this->tratamientoService->obtenerPorCita((int) $citaId);
            return $this->successResponse($tratamientos, 'Tratamientos obtenidos correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function totalCita($citaId)
    {
        try {
            $total = $this->tratamientoService->calcularTotalCita((int) $citaId);
            return $this->successResponse(['total' => $total], 'Total obtenido correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function frecuentes(Request $request)
    {
        try {
            $fechaInicio = $request->query('fecha_inicio');
            $fechaFin = $request->query('fecha_fin');

            if (!$fechaInicio || !$fechaFin) {
                return $this->errorResponse('Las fechas son requeridas', 400);
            }

            $tratamientos = $this->tratamientoService->obtenerTratamientosFrequentes($fechaInicio, $fechaFin);
            return $this->successResponse($tratamientos, 'Tratamientos frecuentes obtenidos');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function rendimiento()
    {
        try {
            $rendimiento = $this->tratamientoService->obtenerRendimiento();
            return $this->successResponse($rendimiento, 'Rendimiento obtenido correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function tipos(Request $request)
    {
        try {
            $categoria = $request->query('categoria');
            $tipos = $this->tratamientoService->obtenerTiposDisponibles($categoria);
            return $this->successResponse($tipos, 'Tipos de tratamiento obtenidos');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function categorias()
    {
        try {
            $categorias = $this->tratamientoService->obtenerCategorias();
            return $this->successResponse($categorias, 'Categorías obtenidas correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function estadisticas()
    {
        try {
            $estadisticas = $this->tratamientoService->obtenerEstadisticas();
            return $this->successResponse($estadisticas, 'Estadísticas obtenidas correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
