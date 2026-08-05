<?php

namespace App\Domain\Odontograma\Repositories;

interface OdontogramaRepositoryInterface
{
    public function obtenerPorPaciente(int $pacienteId, ?int $usuarioId = null): array;
    public function guardarMarca(int $pacienteId, array $datos): array;
    public function actualizarIndicadores(int $pacienteId, array $datos): array;
    public function eliminarMarca(int $marcaId): array;
    public function obtenerCatalogos(): array;
    public function obtenerClavesCondicionesActivas(): array;
}
