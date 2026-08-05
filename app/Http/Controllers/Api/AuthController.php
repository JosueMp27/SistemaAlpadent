<?php

/**
 * Documentacion de archivo:
 * Controlador API que recibe peticiones HTTP, valida entradas y delega en la capa de aplicacion Hexagonal.
 */

namespace App\Http\Controllers\Api;

use App\Application\Usuario\Services\UsuarioApplicationService;
use Illuminate\Http\Request;

class AuthController extends ApiController
{
    public function __construct(
        protected UsuarioApplicationService $usuarioService
    ) {}

    public function login(Request $request)
    {
        try {
            $datos = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            $usuario = $this->usuarioService->validarCredenciales($datos['email'], $datos['password']);

            if (!$usuario) {
                return $this->unauthorizedResponse('Credenciales incorrectas');
            }

            $token = $usuario->createToken('auth_token')->plainTextToken;

            return $this->successResponse([
                'usuario' => $usuario,
                'token' => $token,
                'token_type' => 'Bearer',
            ], 'Login exitoso', 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return $this->successResponse(null, 'Logout exitoso');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function me(Request $request)
    {
        try {
            return $this->successResponse($request->user(), 'Usuario obtenido correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function cambiarPassword(Request $request)
    {
        try {
            $datos = $request->validate([
                'password_actual' => 'required|string|min:6',
                'password_nueva' => 'required|string|min:6|confirmed',
            ]);

            $this->usuarioService->cambiarPassword(
                $request->user()->id,
                $datos['password_actual'],
                $datos['password_nueva']
            );

            return $this->successResponse(null, 'Contraseña cambiada correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function actualizarPerfil(Request $request)
    {
        try {
            $datos = $request->validate([
                'nombre' => 'nullable|string|min:2|max:100',
                'apellido' => 'nullable|string|min:2|max:100',
                'email' => 'nullable|email|unique:usuarios,email,' . $request->user()->id,
            ]);

            $usuario = $this->usuarioService->actualizar($request->user()->id, $datos);
            return $this->successResponse($usuario, 'Perfil actualizado correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function detalles(Request $request)
    {
        try {
            $detalles = $this->usuarioService->obtenerDetalles($request->user()->id);
            return $this->successResponse($detalles, 'Detalles obtenidos correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
