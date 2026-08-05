<?php

/**
 * Documentacion de archivo:
 * Controlador API que recibe peticiones HTTP, valida entradas y delega en la capa de aplicacion Hexagonal.
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreDoctorExternoRequest;
use App\Application\DoctorExterno\Services\DoctorExternoApplicationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class DoctorExternoController extends ApiController
{
    public function __construct(
        protected DoctorExternoApplicationService $doctorService
    ) {}

    public function index(Request $request)
    {
        try {
            $search = $request->query('search');
            $activo = $request->query('activo');

            $doctores = $this->doctorService->listar($search, $activo);

            return $this->successResponse(
                $doctores,
                'Doctores externos obtenidos correctamente'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function activos()
    {
        try {
            $doctores = $this->doctorService->obtenerActivos();

            return $this->successResponse($doctores, 'Doctores externos activos obtenidos correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function store(StoreDoctorExternoRequest $request)
    {
        try {
            $doctor = $this->doctorService->crear($request->validated());

            return $this->successResponse(
                $doctor,
                'Doctor externo creado correctamente',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $doctor = $this->doctorService->obtenerPorId((int) $id);

            return $this->successResponse($doctor, 'Doctor externo obtenido correctamente');
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Doctor externo no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function update(StoreDoctorExternoRequest $request, $id)
    {
        try {
            $doctor = $this->doctorService->actualizar((int) $id, $request->validated());

            return $this->successResponse(
                $doctor,
                'Doctor externo actualizado correctamente'
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Doctor externo no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->doctorService->cambiarEstado((int) $id, false);

            return $this->successResponse(null, 'Doctor externo eliminado correctamente');
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Doctor externo no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function reactivar($id)
    {
        try {
            $doctor = $this->doctorService->cambiarEstado((int) $id, true);

            return $this->successResponse($doctor, 'Doctor externo reactivado correctamente');
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('Doctor externo no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
