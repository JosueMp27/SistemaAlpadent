<?php

namespace App\Application\Usuario\Services;

use App\Domain\Usuario\Repositories\UsuarioRepositoryInterface;
use App\Models\User;

class UsuarioApplicationService
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarioRepository
    ) {}

    public function crear(array $datos): User
    {
        return $this->usuarioRepository->crear($datos);
    }

    public function actualizar(int $usuarioId, array $datos): User
    {
        return $this->usuarioRepository->actualizar($usuarioId, $datos);
    }

    public function cambiarPassword(int $usuarioId, string $passwordActual, string $passwordNueva): bool
    {
        return $this->usuarioRepository->cambiarPassword($usuarioId, $passwordActual, $passwordNueva);
    }

    public function resetearPassword(int $usuarioId, string $passwordNueva): bool
    {
        return $this->usuarioRepository->resetearPassword($usuarioId, $passwordNueva);
    }

    public function desactivar(int $usuarioId): bool
    {
        return $this->usuarioRepository->desactivar($usuarioId);
    }

    public function activar(int $usuarioId): bool
    {
        return $this->usuarioRepository->activar($usuarioId);
    }

    public function obtenerActivos(bool $paginated = true)
    {
        return $this->usuarioRepository->obtenerActivos($paginated);
    }

    public function obtenerTodos(bool $paginated = true)
    {
        return $this->usuarioRepository->obtenerTodos($paginated);
    }

    public function obtenerAdministradores()
    {
        return $this->usuarioRepository->obtenerAdministradores();
    }

    public function obtenerSecretarias()
    {
        return $this->usuarioRepository->obtenerSecretarias();
    }

    public function validarCredenciales(string $email, string $password): ?User
    {
        return $this->usuarioRepository->validarCredenciales($email, $password);
    }

    public function obtenerPorEmail(string $email): ?User
    {
        return $this->usuarioRepository->obtenerPorEmail($email);
    }

    public function obtenerDetalles(int $usuarioId)
    {
        return $this->usuarioRepository->obtenerDetalles($usuarioId);
    }
}
