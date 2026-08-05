<?php

/**
 * Documentacion de archivo:
 * Controlador API que recibe peticiones HTTP, valida entradas y delega en la capa de aplicacion Hexagonal.
 */

namespace App\Http\Controllers\Api;

use App\Application\Odontograma\Services\OdontogramaApplicationService;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentOdontogramaRepository;
use Illuminate\Http\Request;

class OdontogramaController extends ApiController
{
    public function __construct(
        private OdontogramaApplicationService $odontogramaService
    ) {}

    public function showPaciente(Request $request, $pacienteId)
    {
        try {
            $odontograma = $this->odontogramaService->obtenerPorPaciente(
                (int) $pacienteId,
                $request->user()?->id
            );

            return $this->successResponse($odontograma, 'Odontograma obtenido correctamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Paciente no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function guardarMarca(Request $request, $pacienteId)
    {
        try {
            $datos = $request->validate([
                'numero_diente' => 'required|integer',
                'superficie' => 'required|in:' . implode(',', EloquentOdontogramaRepository::SUPERFICIES),
                'condicion' => 'required|in:' . implode(',', $this->odontogramaService->obtenerClavesCondicionesActivas()),
                'tipo_tratamiento_id' => 'nullable|exists:tipos_tratamiento,id',
                'cita_id' => 'nullable|exists:citas,id',
                'usuario_id' => 'required|exists:usuarios,id',
                'observacion' => 'nullable|string|max:1000',
            ]);

            $odontograma = $this->odontogramaService->guardarMarca((int) $pacienteId, $datos);

            return $this->successResponse($odontograma, 'Marca guardada correctamente');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationFailedResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function actualizarIndicadores(Request $request, $pacienteId)
    {
        try {
            $datos = $request->validate([
                'usuario_id' => 'required|exists:usuarios,id',
                'higiene_placa' => 'nullable|integer|min:0|max:3',
                'higiene_calculo' => 'nullable|integer|min:0|max:3',
                'higiene_gingivitis' => 'nullable|integer|min:0|max:1',
                'enfermedad_periodontal' => 'required|in:ninguna,leve,moderada,severa',
                'maloclusion' => 'required|in:ninguna,angle_i,angle_ii,angle_iii',
                'fluorosis' => 'required|in:ninguna,leve,moderada,severa',
                'observaciones' => 'nullable|string|max:1000',
            ]);

            $odontograma = $this->odontogramaService->actualizarIndicadores((int) $pacienteId, $datos);

            return $this->successResponse($odontograma, 'Indicadores actualizados correctamente');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationFailedResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function eliminarMarca($marcaId)
    {
        try {
            $odontograma = $this->odontogramaService->eliminarMarca((int) $marcaId);

            return $this->successResponse($odontograma, 'Marca eliminada correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function catalogos()
    {
        try {
            $catalogos = $this->odontogramaService->obtenerCatalogos();

            return $this->successResponse($catalogos, 'Catalogos del odontograma obtenidos correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
