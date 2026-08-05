<?php

namespace App\Application\Paciente\Services;

use App\Domain\Paciente\Repositories\PacienteRepositoryInterface;
use App\Models\Paciente;
use App\Models\AntecedenteMedico;

class PacienteApplicationService
{
    public function __construct(
        private PacienteRepositoryInterface $pacienteRepository
    ) {}

    public function registrar(array $datos): Paciente
    {
        return $this->pacienteRepository->registrar($datos);
    }

    public function actualizarCompleto(int $pacienteId, array $datos): Paciente
    {
        return $this->pacienteRepository->actualizarCompleto($pacienteId, $datos);
    }

    public function actualizarAntecedentes(int $pacienteId, array $datos): AntecedenteMedico
    {
        return $this->pacienteRepository->actualizarAntecedentes($pacienteId, $datos);
    }

    public function obtenerHistorial(int $pacienteId)
    {
        return $this->pacienteRepository->obtenerHistorial($pacienteId);
    }

    public function obtenerSaldoTotal(int $pacienteId): float
    {
        return $this->pacienteRepository->obtenerSaldoTotal($pacienteId);
    }

    public function obtenerDeudores(bool $paginated = true)
    {
        return $this->pacienteRepository->obtenerDeudores($paginated);
    }

    public function desactivar(int $pacienteId): bool
    {
        return $this->pacienteRepository->desactivar($pacienteId);
    }

    public function activar(int $pacienteId): bool
    {
        return $this->pacienteRepository->activar($pacienteId);
    }

    public function buscar(string $termino, bool $paginated = true)
    {
        return $this->pacienteRepository->buscar($termino, $paginated);
    }

    public function obtenerPorId(int $pacienteId): Paciente
    {
        return $this->pacienteRepository->obtenerPorId($pacienteId);
    }
}
