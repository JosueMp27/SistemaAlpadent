<?php

/**
 * Documentacion de archivo:
 * Controlador API que recibe peticiones HTTP, valida entradas y delega en la capa de aplicacion Hexagonal.
 */

namespace App\Http\Controllers\Api;

use App\Application\Usuario\Services\UsuarioApplicationService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ConfiguracionUsuarioController extends ApiController
{
    public function __construct(
        protected UsuarioApplicationService $usuarioService
    ) {}

    public function index(Request $request)
    {
        try {
            $search = $request->query('search');
            $rol = $request->query('rol');
            $activo = $request->query('activo');

            $query = User::query()
                ->orderBy('created_at', 'desc');

            if ($search) {
                $query->where(function ($subquery) use ($search) {
                    $subquery->where('nombre', 'like', "%{$search}%")
                        ->orWhere('apellido', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($rol) {
                $query->where('rol', $rol);
            }

            if ($activo !== null && $activo !== '') {
                $query->where('activo', filter_var($activo, FILTER_VALIDATE_BOOLEAN));
            }

            return $this->successResponse($query->paginate(15), 'Usuarios obtenidos correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $datos = $this->validar($request);
            $usuario = $this->usuarioService->crear($datos);

            return $this->successResponse($usuario, 'Usuario creado correctamente', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationFailedResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $detalles = $this->usuarioService->obtenerDetalles((int) $id);
            return $this->successResponse($detalles['usuario'], 'Usuario obtenido correctamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Usuario no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $datos = $this->validar($request, (int) $id, false);

            if (empty($datos['password'])) {
                unset($datos['password']);
            }

            $usuario = $this->usuarioService->actualizar((int) $id, $datos);

            return $this->successResponse($usuario, 'Usuario actualizado correctamente');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationFailedResponse($e->errors());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Usuario no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->usuarioService->desactivar((int) $id);

            return $this->successResponse(null, 'Usuario desactivado correctamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Usuario no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function reactivar($id)
    {
        try {
            $this->usuarioService->activar((int) $id);
            $usuario = User::findOrFail($id);

            return $this->successResponse($usuario, 'Usuario reactivado correctamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Usuario no encontrado');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    private function validar(Request $request, ?int $id = null, bool $passwordRequerida = true): array
    {
        $request->merge([
            'nombre' => $request->nombre ? trim((string) $request->nombre) : null,
            'apellido' => $request->apellido ? trim((string) $request->apellido) : null,
            'email' => $request->email ? trim((string) $request->email) : null,
            'activo' => filter_var($request->activo, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
        ]);

        return $request->validate([
            'nombre' => 'required|string|min:2|max:100',
            'apellido' => 'required|string|min:2|max:100',
            'email' => ['required', 'email', 'max:150', Rule::unique('usuarios', 'email')->ignore($id)],
            'password' => [$passwordRequerida ? 'required' : 'nullable', 'string', 'min:6'],
            'rol' => 'required|in:administrador,secretaria',
            'activo' => 'boolean',
        ], [
            'email.unique' => 'Ya existe un usuario con este correo.',
            'rol.in' => 'El rol debe ser Administrador o Secretaria/Asistente.',
        ]);
    }
}
