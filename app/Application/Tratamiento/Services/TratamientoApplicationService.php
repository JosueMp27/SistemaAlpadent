<?php

namespace App\Application\Tratamiento\Services;

use App\Domain\Tratamiento\Repositories\TratamientoRepositoryInterface;
use App\Models\TratamientoRealizado;

class TratamientoApplicationService
{
    public function __construct(
        private TratamientoRepositoryInterface $tratamientoRepository
    ) {}

    public function registrar(array $datos): TratamientoRealizado
    {
        return $this->tratamientoRepository->registrar($datos);
    }

    public function actualizar(int $tratamientoId, array $datos): TratamientoRealizado
    {
        return $this->tratamientoRepository->actualizar($tratamientoId, $datos);
    }

    public function eliminar(int $tratamientoId): bool
    {
        return $this->tratamientoRepository->eliminar($tratamientoId);
    }

    public function obtenerPorCita(int $citaId)
    {
        return $this->tratamientoRepository->obtenerPorCita($citaId);
    }

    public function calcularTotalCita(int $citaId): float
    {
        return $this->tratamientoRepository->calcularTotalCita($citaId);
    }

    public function obtenerTratamientosFrequentes($fechaInicio, $fechaFin)
    {
        return $this->tratamientoRepository->obtenerTratamientosFrequentes($fechaInicio, $fechaFin);
    }

    public function obtenerRendimiento()
    {
        return $this->tratamientoRepository->obtenerRendimiento();
    }

    public function obtenerTiposDisponibles($categoria = null)
    {
        return $this->tratamientoRepository->obtenerTiposDisponibles($categoria);
    }

    public function obtenerEstadisticas()
    {
        return $this->tratamientoRepository->obtenerEstadisticas();
    }

    public function obtenerCategorias()
    {
        return $this->tratamientoRepository->obtenerCategorias();
    }
}
