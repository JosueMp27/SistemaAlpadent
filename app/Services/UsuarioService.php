<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

/**
 * Documentacion de clase:
 * Servicio de negocio que concentra reglas, transacciones y consultas Eloquent para mantener limpios los controladores.
 */
class UsuarioService
{
    /**
     * Crea un nuevo usuario
     */
    /**
     * Documentacion: Ejecuta la operacion crear.
     * Como lo hace: Coordina modelos Eloquent, calculos y transacciones para aplicar una regla de negocio.
     */
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

    /**
     * Actualiza un usuario
     */
    /**
     * Documentacion: Ejecuta la operacion actualizar.
     * Como lo hace: Coordina modelos Eloquent, calculos y transacciones para aplicar una regla de negocio.
     */
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

    /**
     * Cambia la contraseña de un usuario
     */
    /**
     * Documentacion: Ejecuta la operacion cambiar password.
     * Como lo hace: Coordina modelos Eloquent, calculos y transacciones para aplicar una regla de negocio.
     */
    public function cambiarPassword(int $usuarioId, string $passwordActual, string $passwordNueva): bool
    {
        $usuario = User::findOrFail($usuarioId);

        if (!Hash::check($passwordActual, $usuario->password)) {
            throw new \Exception('Contraseña actual incorrecta.');
        }

        return $usuario->update(['password' => Hash::make($passwordNueva)]);
    }

    /**
     * Resetea la contraseña de un usuario (por admin)
     */
    /**
     * Documentacion: Ejecuta la operacion resetear password.
     * Como lo hace: Coordina modelos Eloquent, calculos y transacciones para aplicar una regla de negocio.
     */
    public function resetearPassword(int $usuarioId, string $passwordNueva): bool
    {
        $usuario = User::findOrFail($usuarioId);
        return $usuario->update(['password' => Hash::make($passwordNueva)]);
    }

    /**
     * Desactiva un usuario
     */
    /**
     * Documentacion: Desactiva un registro sin borrarlo fisicamente.
     * Como lo hace: Actualiza la bandera activo para conservar el historial asociado.
     */
    public function desactivar(int $usuarioId): bool
    {
        $usuario = User::findOrFail($usuarioId);
        return $usuario->update(['activo' => false]);
    }

    /**
     * Activa un usuario
     */
    /**
     * Documentacion: Activa nuevamente un registro.
     * Como lo hace: Actualiza la bandera activo para que vuelva a aparecer en listados operativos.
     */
    public function activar(int $usuarioId): bool
    {
        $usuario = User::findOrFail($usuarioId);
        return $usuario->update(['activo' => true]);
    }

    /**
     * Obtiene todos los usuarios activos
     */
    /**
     * Documentacion: Genera un reporte solicitado.
     * Como lo hace: Valida el tipo, calcula filas y resumen, y agrega metadatos de usuario y fecha.
     */
    public function obtenerActivos($paginated = true)
    {
        $query = User::where('activo', true)->orderBy('nombre', 'asc');

        if ($paginated) {
            return $query->paginate(15);
        }

        return $query->get();
    }

    /**
     * Obtiene todos los usuarios
     */
    /**
     * Documentacion: Genera un reporte solicitado.
     * Como lo hace: Valida el tipo, calcula filas y resumen, y agrega metadatos de usuario y fecha.
     */
    public function obtenerTodos($paginated = true)
    {
        $query = User::orderBy('nombre', 'asc');

        if ($paginated) {
            return $query->paginate(15);
        }

        return $query->get();
    }

    /**
     * Obtiene administradores
     */
    /**
     * Documentacion: Genera un reporte solicitado.
     * Como lo hace: Valida el tipo, calcula filas y resumen, y agrega metadatos de usuario y fecha.
     */
    public function obtenerAdministradores()
    {
        return User::activos()
            ->administradores()
            ->get();
    }

    /**
     * Obtiene secretarias
     */
    /**
     * Documentacion: Genera un reporte solicitado.
     * Como lo hace: Valida el tipo, calcula filas y resumen, y agrega metadatos de usuario y fecha.
     */
    public function obtenerSecretarias()
    {
        return User::activos()
            ->secretarias()
            ->get();
    }

    /**
     * Valida credenciales de login
     */
    /**
     * Documentacion: Normaliza y valida datos del formulario.
     * Como lo hace: Limpia cadenas, convierte booleanos y aplica reglas de Laravel con mensajes especificos.
     */
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

    /**
     * Obtiene un usuario por su email
     */
    /**
     * Documentacion: Genera un reporte solicitado.
     * Como lo hace: Valida el tipo, calcula filas y resumen, y agrega metadatos de usuario y fecha.
     */
    public function obtenerPorEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Obtiene información completa de un usuario con estadísticas
     */
    /**
     * Documentacion: Obtiene detalles extendidos de un registro.
     * Como lo hace: Carga relaciones necesarias para que el frontend no haga consultas adicionales.
     */
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
