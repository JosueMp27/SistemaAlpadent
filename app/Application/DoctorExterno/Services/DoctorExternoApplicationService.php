<?php

namespace App\Application\DoctorExterno\Services;

use App\Domain\DoctorExterno\Repositories\DoctorExternoRepositoryInterface;
use App\Models\DoctorExterno;

class DoctorExternoApplicationService
{
    public function __construct(
        private DoctorExternoRepositoryInterface $doctorExternoRepository
    ) {}

    public function listar(?string $search = null, $activo = null)
    {
        return $this->doctorExternoRepository->listar($search, $activo);
    }

    public function obtenerActivos()
    {
        return $this->doctorExternoRepository->obtenerActivos();
    }

    public function crear(array $datos): DoctorExterno
    {
        return $this->doctorExternoRepository->crear($datos);
    }

    public function obtenerPorId(int $id): DoctorExterno
    {
        return $this->doctorExternoRepository->obtenerPorId($id);
    }

    public function actualizar(int $id, array $datos): DoctorExterno
    {
        return $this->doctorExternoRepository->actualizar($id, $datos);
    }

    public function cambiarEstado(int $id, bool $activo): DoctorExterno
    {
        return $this->doctorExternoRepository->cambiarEstado($id, $activo);
    }
}
