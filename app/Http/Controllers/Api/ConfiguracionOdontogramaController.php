<?php

/**
 * Documentacion de archivo:
 * Controlador API que recibe peticiones HTTP, valida entradas, llama servicios o modelos y responde JSON para el frontend.
 *
 * Mantiene la documentacion dentro del codigo para facilitar el estudio y mantenimiento del proyecto.
 */

namespace App\Http\Controllers\Api;

use App\Models\OdontogramaCondicion;
use App\Models\OdontogramaMarca;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Documentacion de clase:
 * Controlador API que recibe peticiones HTTP, valida entradas, llama servicios o modelos y responde JSON para el frontend.
 */
class ConfiguracionOdontogramaController extends ApiController
{
    /**
     * Documentacion: Lista registros del recurso principal.
     * Como lo hace: Construye una consulta, aplica filtros de la peticion y devuelve una coleccion paginada.
     */
    public function index(Request $request)
    {
        try {
            $search = $request->query('search');
            $activo = $request->query('activo');

            $query = OdontogramaCondicion::query()
                ->orderBy('grupo')
                ->orderBy('label');

            if ($search) {
                $query->where(function ($subquery) use ($search) {
                    $subquery->where('clave', 'like', "%{$search}%")
                        ->orWhere('label', 'like', "%{$search}%")
                        ->orWhere('grupo', 'like', "%{$search}%");
                });
            }

            if ($activo !== null && $activo !== '') {
                $query->where('activo', filter_var($activo, FILTER_VALIDATE_BOOLEAN));
            }

            return $this->successResponse($query->paginate(15), 'Opciones de odontograma obtenidas correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Documentacion: Crea un nuevo registro.
     * Como lo hace: Valida la entrada, delega la creacion al servicio o modelo y devuelve codigo 201 cuando todo sale bien.
     */
    public function store(Request $request)
    {
        try {
            $datos = $this->validar($request);
            $opcion = OdontogramaCondicion::create($datos);

            return $this->successResponse($opcion, 'Opcion de odontograma creada correctamente', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationFailedResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Documentacion: Muestra el detalle de un registro especifico.
     * Como lo hace: Busca el modelo por ID, carga relaciones necesarias y responde con JSON estandar.
     */
    public function show($id)
    {
        try {
            return $this->successResponse(OdontogramaCondicion::findOrFail($id), 'Opcion obtenida correctamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Opcion no encontrada');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Documentacion: Actualiza un registro existente.
     * Como lo hace: Busca el registro, valida cambios permitidos y persiste los campos modificados.
     */
    public function update(Request $request, $id)
    {
        try {
            $opcion = OdontogramaCondicion::findOrFail($id);
            $datos = $this->validar($request, $opcion->id);
            $opcion->update($datos);

            return $this->successResponse($opcion->fresh(), 'Opcion de odontograma actualizada correctamente');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationFailedResponse($e->errors());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Opcion no encontrada');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Documentacion: Elimina o desactiva un registro.
     * Como lo hace: Ubica el registro y aplica borrado logico cuando se necesita conservar historial clinico o financiero.
     */
    public function destroy($id)
    {
        try {
            $opcion = OdontogramaCondicion::findOrFail($id);
            $usos = OdontogramaMarca::where('condicion', $opcion->clave)->count();

            if ($usos > 0) {
                $opcion->update(['activo' => false]);
                return $this->successResponse(null, 'La opcion fue desactivada porque tiene marcas historicas asociadas');
            }

            $opcion->delete();

            return $this->successResponse(null, 'Opcion eliminada correctamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Opcion no encontrada');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Documentacion: Reactiva un registro previamente desactivado.
     * Como lo hace: Cambia la bandera activo a verdadero y devuelve el registro actualizado.
     */
    public function reactivar($id)
    {
        try {
            $opcion = OdontogramaCondicion::findOrFail($id);
            $opcion->update(['activo' => true]);

            return $this->successResponse($opcion, 'Opcion reactivada correctamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Opcion no encontrada');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Documentacion: Normaliza y valida datos del formulario.
     * Como lo hace: Limpia cadenas, convierte booleanos y aplica reglas de Laravel con mensajes especificos.
     */
    private function validar(Request $request, ?int $id = null): array
    {
        $request->merge([
            'clave' => $request->clave ? trim((string) $request->clave) : null,
            'label' => $request->label ? trim((string) $request->label) : null,
            'grupo' => $request->grupo ? trim((string) $request->grupo) : null,
            'activo' => filter_var($request->activo, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
        ]);

        return $request->validate([
            'clave' => [
                'required',
                'string',
                'min:2',
                'max:40',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('odontograma_condiciones', 'clave')->ignore($id),
            ],
            'label' => 'required|string|min:2|max:100',
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'grupo' => 'required|in:neutro,cpo_c,cpo_o,cpo_p,tratamiento,hallazgo',
            'activo' => 'boolean',
        ], [
            'clave.regex' => 'La clave solo puede tener minusculas, numeros y guion bajo.',
            'clave.unique' => 'Ya existe una opcion con esta clave.',
            'color.regex' => 'El color debe tener formato hexadecimal, por ejemplo #0d6efd.',
        ]);
    }
}
