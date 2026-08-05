<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Usuario\Repositories\UsuarioRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EloquentUsuarioRepository implements UsuarioRepositoryInterface
{
    public function crear(array $datos): User
    {
        return DB::transaction(function () use ($datos) {
            return User::create([
                'nombre' => $datos['nombre'],
                'apellido' => $datos['apellido'],
                'email' => $datos['email'],
                'password' => Hash::make($datos['password']),
                'rol' => $datos['rol'] ?? 'secretaria',
                'activo' => $datos['activo'] ?? true,
            ]);
        });
    }

    public function actualizar(int $usuarioId, array $datos): User
    {
        $usuario = User::findOrFail($usuarioId);

        return DB::transaction(function () use ($usuario, $datos) {
            $usuario->update([
                'nombre' => $datos['nombre'] ?? $usuario->nombre,
                'apellido' => $datos['apellido'] ?? $usuario->apellido,
                'email' => $datos['email'] ?? $usuario->email,
                'rol' => $datos['rol'] ?? $usuario->rol,
            ]);

            return $usuario->fresh();
        });
    }

    public function cambiarPassword(int $usuarioId, string $passwordActual, string $passwordNueva): bool
    {
        $usuario = User::findOrFail($usuarioId);

        if (!Hash::check($passwordActual, $usuario->password)) {
            throw new \Exception('Contraseña actual incorrecta.');
        }

        return $usuario->update(['password' => Hash::make($passwordNueva)]);
    }

    public function resetearPassword(int $usuarioId, string $passwordNueva): bool
    {
        $usuario = User::findOrFail($usuarioId);
        return $usuario->update(['password' => Hash::make($passwordNueva)]);
    }

    public function desactivar(int $usuarioId): bool
    {
        $usuario = User::findOrFail($usuarioId);
        return $usuario->update(['activo' => false]);
    }

    public function activar(int $usuarioId): bool
    {
        $usuario = User::findOrFail($usuarioId);
        return $usuario->update(['activo' => true]);
    }

    public function obtenerActivos(bool $paginated = true)
    {
        $query = User::where('activo', true)->orderBy('nombre', 'asc');

        if ($paginated) {
            return $query->paginate(15);
        }

        return $query->get();
    }

    public function obtenerTodos(bool $paginated = true)
    {
        $query = User::orderBy('nombre', 'asc');

        if ($paginated) {
            return $query->paginate(15);
        }

        return $query->get();
    }

    public function obtenerAdministradores()
    {
        return User::activos()
            ->administradores()
            ->get();
    }

    public function obtenerSecretarias()
    {
        return User::activos()
            ->secretarias()
            ->get();
    }

    public function validarCredenciales(string $email, string $password): ?User
    {
        $usuario = User::where('email', $email)
            ->where('activo', true)
            ->first();

        if (!$usuario || !Hash::check($password, $usuario->password)) {
            return null;
        }

        return $usuario;
    }

    public function obtenerPorEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function obtenerDetalles(int $usuarioId)
    {
        $usuario = User::with([
            'citas',
            'diagnosticos',
            'pagos',
            'ventasProducto'
        ])->findOrFail($usuarioId);

        return [
            'usuario' => $usuario,
            'estadisticas' => [
                'total_citas' => $usuario->citas()->count(),
                'total_diagnosticos' => $usuario->diagnosticos()->count(),
                'total_pagos_procesados' => $usuario->pagos()->count(),
                'total_ventas' => $usuario->ventasProducto()->sum('total'),
            ]
        ];
    }
}
