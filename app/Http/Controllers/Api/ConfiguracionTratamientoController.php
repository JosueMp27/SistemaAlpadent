<?php

/**
 * Documentacion de archivo:
 * Controlador API que recibe peticiones HTTP, valida entradas, llama servicios o modelos y responde JSON para el frontend.
 *
 * Mantiene la documentacion dentro del codigo para facilitar el estudio y mantenimiento del proyecto.
 */

namespace App\Http\Controllers\Api;

use App\Models\TipoTratamiento;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Documentacion de clase:
 * Controlador API que recibe peticiones HTTP, valida entradas, llama servicios o modelos y responde JSON para el frontend.
 */
class ConfiguracionTratamientoController extends ApiController
{
    private array $categorias = [
        'operatoria',
        'limpieza',
        'periodoncia',
        'endodoncia',
        'exodoncia',
        'cirugia',
        'protesis_removible',
        'protesis_fija',
        'ortodoncia',
        'implantologia',
        'rayos_x',
        'otros',
    ];

    /**
     * Documentacion: Lista registros del recurso principal.
     * Como lo hace: Construye una consulta, aplica filtros de la peticion y devuelve una coleccion paginada.
     */
    public function index(Request $request)
    {
        try {
            $search = $request->query('search');
            $categoria = $request->query('categoria');
            $activo = $request->query('activo');

            $query = TipoTratamiento::query()
                ->orderBy('categoria')
                ->orderBy('nombre');

            if ($search) {
                $query->where(function ($subquery) use ($search) {
                    $subquery->where('nombre', 'like', "%{$search}%")
                        ->orWhere('categoria', 'like', "%{$search}%")
                        ->orWhere('descripcion', 'like', "%{$search}%");
                });
            }

            if ($categoria) {
                $query->where('categoria', $categoria);
            }

            if ($activo !== null && $activo !== '') {
                $query->where('activo', filter_var($activo, FILTER_VALIDATE_BOOLEAN));
            }

            return $this->successResponse([
                'categorias' => $this->categorias,
                'tratamientos' => $query->paginate(15),
            ], 'Tratamientos obtenidos correctamente');
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
            $tratamiento = TipoTratamiento::create($this->validar($request));

            return $this->successResponse($tratamiento, 'Tratamiento creado correctamente', 201);
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
            return $this->successResponse(TipoTratamiento::findOrFail($id), 'Tratamiento obtenido correctamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Tratamiento no encontrado');
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
            $tratamiento = TipoTratamiento::findOrFail($id);
            $tratamiento->update($this->validar($request, $tratamiento->id));

            return $this->successResponse($tratamiento->fresh(), 'Tratamiento actualizado correctamente');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationFailedResponse($e->errors());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Tratamiento no encontrado');
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
            $tratamiento = TipoTratamiento::findOrFail($id);
            $tratamiento->update(['activo' => false]);

            return $this->successResponse(null, 'Tratamiento desactivado correctamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Tratamiento no encontrado');
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
            $tratamiento = TipoTratamiento::findOrFail($id);
            $tratamiento->update(['activo' => true]);

            return $this->successResponse($tratamiento, 'Tratamiento reactivado correctamente');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Tratamiento no encontrado');
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
            'nombre' => $request->nombre ? trim((string) $request->nombre) : null,
            'categoria' => $request->categoria ? trim((string) $request->categoria) : null,
            'descripcion' => $request->descripcion ? trim((string) $request->descripcion) : null,
            'activo' => filter_var($request->activo, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
        ]);

        return $request->validate([
            'nombre' => ['required', 'string', 'min:2', 'max:150', Rule::unique('tipos_tratamiento', 'nombre')->ignore($id)],
            'categoria' => ['required', Rule::in($this->categorias)],
            'precio' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:1000',
            'activo' => 'boolean',
        ], [
            'nombre.unique' => 'Ya existe un tratamiento con este nombre.',
            'categoria.in' => 'La categoria seleccionada no es valida.',
        ]);
    }
}
