<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\DoctorExterno\Repositories\DoctorExternoRepositoryInterface;
use App\Models\DoctorExterno;

class EloquentDoctorExternoRepository implements DoctorExternoRepositoryInterface
{
    public function listar(?string $search = null, $activo = null)
    {
        $query = DoctorExterno::query()
            ->withCount('citas')
            ->orderBy('apellido')
            ->orderBy('nombre');

        if ($search) {
            $query->where(function ($subquery) use ($search) {
                $subquery->where('nombre', 'like', "%{$search}%")
                    ->orWhere('apellido', 'like', "%{$search}%")
                    ->orWhere('especialidad', 'like', "%{$search}%")
                    ->orWhere('telefono', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($activo !== null && $activo !== '') {
            $query->where('activo', filter_var($activo, FILTER_VALIDATE_BOOLEAN));
        }

        return $query->paginate(15);
    }

    public function obtenerActivos()
    {
        return DoctorExterno::activos()
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->get();
    }

    public function crear(array $datos): DoctorExterno
    {
        return DoctorExterno::create($datos)->fresh();
    }

    public function obtenerPorId(int $id): DoctorExterno
    {
        return DoctorExterno::withCount('citas')->findOrFail($id);
    }

    public function actualizar(int $id, array $datos): DoctorExterno
    {
        $doctor = DoctorExterno::findOrFail($id);
        $doctor->update($datos);
        return $doctor->fresh();
    }

    public function cambiarEstado(int $id, bool $activo): DoctorExterno
    {
        $doctor = DoctorExterno::findOrFail($id);
        $doctor->update(['activo' => $activo]);
        return $doctor->fresh();
    }
}
