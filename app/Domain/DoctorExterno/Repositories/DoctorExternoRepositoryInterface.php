<?php

namespace App\Domain\DoctorExterno\Repositories;

use App\Models\DoctorExterno;

interface DoctorExternoRepositoryInterface
{
    public function listar(?string $search = null, $activo = null);
    public function obtenerActivos();
    public function crear(array $datos): DoctorExterno;
    public function obtenerPorId(int $id): DoctorExterno;
    public function actualizar(int $id, array $datos): DoctorExterno;
    public function cambiarEstado(int $id, bool $activo): DoctorExterno;
}
