<?php

namespace App\Domain\Usuario\Repositories;

use App\Models\User;

interface UsuarioRepositoryInterface
{
    public function crear(array $datos): User;
    public function actualizar(int $usuarioId, array $datos): User;
    public function cambiarPassword(int $usuarioId, string $passwordActual, string $passwordNueva): bool;
    public function resetearPassword(int $usuarioId, string $passwordNueva): bool;
    public function desactivar(int $usuarioId): bool;
    public function activar(int $usuarioId): bool;
    public function obtenerActivos(bool $paginated = true);
    public function obtenerTodos(bool $paginated = true);
    public function obtenerAdministradores();
    public function obtenerSecretarias();
    public function validarCredenciales(string $email, string $password): ?User;
    public function obtenerPorEmail(string $email): ?User;
    public function obtenerDetalles(int $usuarioId);
}
