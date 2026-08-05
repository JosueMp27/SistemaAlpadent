<?php

/**
 * Documentacion de archivo:
 * Controlador API que recibe peticiones HTTP, valida entradas y delega en la capa de aplicacion Hexagonal.
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\StorePacienteRequest;
use App\Application\Paciente\Services\PacienteApplicationService;
use Illuminate\Http\Request;

class PacienteController extends ApiController
{
    public function __construct(
        protected PacienteApplicationService $pacienteService
    ) {}

    public function index(Request $request)
    {
        try {
            $search = $request->query('search');

            if ($search) {
                $pacientes = $this->pacienteService->buscar($search);
            } else {
                $pacientes = $this->pacienteService->buscar('', true);
            }

            return $this->successResponse($pacientes, 'Pacientes obtenidos correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function store(StorePacienteRequest $request)
    {
        try {
            $paciente = $this->pacienteService->registrar($request->validated());
            return $this->successResponse($paciente, 'Paciente creado correctamente', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $paciente = $this->pacienteService->obtenerPorId((int) $id);
            return $this->successResponse($paciente, 'Paciente obtenido correctamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Paciente no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function update(StorePacienteRequest $request, $id)
    {
        try {
            $paciente = $this->pacienteService->actualizarCompleto((int) $id, $request->validated());
            return $this->successResponse($paciente, 'Paciente actualizado correctamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Paciente no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->pacienteService->desactivar((int) $id);
            return $this->successResponse(null, 'Paciente desactivado correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function historial($id)
    {
        try {
            $historial = $this->pacienteService->obtenerHistorial((int) $id);
            return $this->successResponse($historial, 'Historial obtenido correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function saldo($id)
    {
        try {
            $saldo = $this->pacienteService->obtenerSaldoTotal((int) $id);
            return $this->successResponse(['saldo' => $saldo], 'Saldo obtenido correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function deudores(Request $request)
    {
        try {
            $deudores = $this->pacienteService->obtenerDeudores();
            return $this->successResponse($deudores, 'Deudores obtenidos correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function reactivar($id)
    {
        try {
            $this->pacienteService->activar((int) $id);
            $paciente = $this->pacienteService->obtenerPorId((int) $id);
            return $this->successResponse($paciente, 'Paciente reactivado correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}