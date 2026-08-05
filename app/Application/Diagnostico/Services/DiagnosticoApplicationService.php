<?php

namespace App\Application\Diagnostico\Services;

use App\Domain\Diagnostico\Repositories\DiagnosticoRepositoryInterface;
use App\Models\Diagnostico;
use App\Models\DienteDiagnostico;

class DiagnosticoApplicationService
{
    public function __construct(
        private DiagnosticoRepositoryInterface $diagnosticoRepository
    ) {}

    public function registrar(array $datos): Diagnostico
    {
        return $this->diagnosticoRepository->registrar($datos);
    }

    public function agregarDiente(int $diagnosticoId, array $datos): DienteDiagnostico
    {
        return $this->diagnosticoRepository->agregarDiente($diagnosticoId, $datos);
    }

    public function actualizarDiente(int $dienteId, array $datos): DienteDiagnostico
    {
        return $this->diagnosticoRepository->actualizarDiente($dienteId, $datos);
    }

    public function eliminarDiente(int $dienteId): bool
    {
        return $this->diagnosticoRepository->eliminarDiente($dienteId);
    }

    public function obtenerPorCita(int $citaId)
    {
        return $this->diagnosticoRepository->obtenerPorCita($citaId);
    }

    public function obtenerOdontograma(int $diagnosticoId)
    {
        return $this->diagnosticoRepository->obtenerOdontograma($diagnosticoId);
    }

    public function obtenerRecientes(int $limite = 10)
    {
        return $this->diagnosticoRepository->obtenerRecientes($limite);
    }

    public function obtenerHistorialPaciente(int $pacienteId): array
    {
        return $this->diagnosticoRepository->obtenerHistorialPaciente($pacienteId);
    }

    public function obtenerEstadisticas()
    {
        return $this->diagnosticoRepository->obtenerEstadisticas();
    }
}
