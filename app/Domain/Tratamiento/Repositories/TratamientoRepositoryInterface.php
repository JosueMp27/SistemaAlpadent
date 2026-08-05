<?php

namespace App\Domain\Tratamiento\Repositories;

use App\Models\TratamientoRealizado;

interface TratamientoRepositoryInterface
{
    public function registrar(array $datos): TratamientoRealizado;
    public function actualizar(int $tratamientoId, array $datos): TratamientoRealizado;
    public function eliminar(int $tratamientoId): bool;
    public function obtenerPorCita(int $citaId);
    public function calcularTotalCita(int $citaId): float;
    public function obtenerTratamientosFrequentes($fechaInicio, $fechaFin);
    public function obtenerRendimiento();
    public function obtenerTiposDisponibles($categoria = null);
    public function obtenerEstadisticas();
    public function obtenerCategorias();
}
