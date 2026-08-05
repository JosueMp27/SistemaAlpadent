<?php

namespace App\Application\Cita\Services;

use App\Domain\Cita\Repositories\CitaRepositoryInterface;
use App\Models\Cita;

class CitaApplicationService
{
    public function __construct(
        private CitaRepositoryInterface $citaRepository
    ) {}

    public function agendar(array $datos): Cita
    {
        return $this->citaRepository->agendar($datos);
    }

    public function reagendar(int $citaId, array $datos): Cita
    {
        return $this->citaRepository->reagendar($citaId, $datos);
    }

    public function cancelar(int $citaId, string $estado, ?string $observaciones = null): Cita
    {
        return $this->citaRepository->cancelar($citaId, $estado, $observaciones);
    }

    public function completar(int $citaId): Cita
    {
        return $this->citaRepository->completar($citaId);
    }

    public function iniciar(int $citaId): Cita
    {
        return $this->citaRepository->iniciar($citaId);
    }

    public function obtenerAgendaHoy()
    {
        return $this->citaRepository->obtenerAgendaHoy();
    }

    public function obtenerProximas(int $dias = 7, bool $paginated = true)
    {
        return $this->citaRepository->obtenerProximas($dias, $paginated);
    }

    public function obtenerCitasPaciente(int $pacienteId, bool $paginated = true)
    {
        return $this->citaRepository->obtenerCitasPaciente($pacienteId, $paginated);
    }

    public function obtenerConflicto($fechaInicio, ?int $excluirCitaId = null): ?Cita
    {
        return $this->citaRepository->obtenerConflicto($fechaInicio, $excluirCitaId);
    }

    public function verificarDisponibilidad($fechaInicio, ?int $excluirCitaId = null): bool
    {
        return $this->citaRepository->verificarDisponibilidad($fechaInicio, $excluirCitaId);
    }

    public function obtenerEstadisticas(?int $anio = null)
    {
        return $this->citaRepository->obtenerEstadisticas($anio);
    }

    public function obtenerPorId(int $citaId): Cita
    {
        return $this->citaRepository->obtenerPorId($citaId);
    }
}
