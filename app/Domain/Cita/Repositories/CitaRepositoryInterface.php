<?php

namespace App\Domain\Cita\Repositories;

use App\Models\Cita;

interface CitaRepositoryInterface
{
    public function agendar(array $datos): Cita;
    public function reagendar(int $citaId, array $datos): Cita;
    public function cancelar(int $citaId, string $estado, ?string $observaciones = null): Cita;
    public function completar(int $citaId): Cita;
    public function iniciar(int $citaId): Cita;
    public function obtenerAgendaHoy();
    public function obtenerProximas(int $dias = 7, bool $paginated = true);
    public function obtenerCitasPaciente(int $pacienteId, bool $paginated = true);
    public function obtenerConflicto($fechaInicio, ?int $excluirCitaId = null): ?Cita;
    public function verificarDisponibilidad($fechaInicio, ?int $excluirCitaId = null): bool;
    public function obtenerEstadisticas(?int $anio = null);
    public function obtenerPorId(int $citaId): Cita;
}
