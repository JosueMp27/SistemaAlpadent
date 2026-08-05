<?php

namespace App\Application\Odontograma\Services;

use App\Domain\Odontograma\Repositories\OdontogramaRepositoryInterface;

class OdontogramaApplicationService
{
    public function __construct(
        private OdontogramaRepositoryInterface $odontogramaRepository
    ) {}

    public function obtenerPorPaciente(int $pacienteId, ?int $usuarioId = null): array
    {
        return $this->odontogramaRepository->obtenerPorPaciente($pacienteId, $usuarioId);
    }

    public function guardarMarca(int $pacienteId, array $datos): array
    {
        return $this->odontogramaRepository->guardarMarca($pacienteId, $datos);
    }

    public function actualizarIndicadores(int $pacienteId, array $datos): array
    {
        return $this->odontogramaRepository->actualizarIndicadores($pacienteId, $datos);
    }

    public function eliminarMarca(int $marcaId): array
    {
        return $this->odontogramaRepository->eliminarMarca($marcaId);
    }

    public function obtenerCatalogos(): array
    {
        return $this->odontogramaRepository->obtenerCatalogos();
    }

    public function obtenerClavesCondicionesActivas(): array
    {
        return $this->odontogramaRepository->obtenerClavesCondicionesActivas();
    }
}
