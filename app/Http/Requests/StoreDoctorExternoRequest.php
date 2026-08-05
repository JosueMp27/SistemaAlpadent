<?php

/**
 * Documentacion de archivo:
 * FormRequest que normaliza datos de entrada y aplica reglas de validacion antes de llegar al controlador.
 *
 * Mantiene la documentacion dentro del codigo para facilitar el estudio y mantenimiento del proyecto.
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Documentacion de clase:
 * FormRequest que normaliza datos de entrada y aplica reglas de validacion antes de llegar al controlador.
 */
class StoreDoctorExternoRequest extends FormRequest
{
    /**
     * Documentacion: Autoriza el uso del FormRequest.
     * Como lo hace: Devuelve verdadero para permitir que la ruta protegida por middleware valide los datos.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Documentacion: Prepara datos antes de validar.
     * Como lo hace: Recorta cadenas y normaliza booleanos para que las reglas reciban valores consistentes.
     */
    protected function prepareForValidation(): void
    {
        $datos = [];

        foreach (['nombre', 'apellido', 'especialidad', 'telefono', 'email'] as $campo) {
            if ($this->has($campo)) {
                $valor = $this->input($campo);
                $datos[$campo] = $valor !== null ? trim((string) $valor) : null;
            }
        }

        if ($this->has('activo')) {
            $datos['activo'] = filter_var(
                $this->input('activo'),
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            ) ?? true;
        }

        $this->merge($datos);
    }

    /**
     * Documentacion: Define reglas de validacion.
     * Como lo hace: Devuelve el arreglo de reglas que Laravel aplica al request.
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|min:2|max:100',
            'apellido' => 'required|string|min:2|max:100',
            'especialidad' => 'required|string|min:2|max:150',
            'telefono' => 'nullable|string|regex:/^[0-9+() -]{7,20}$/',
            'email' => 'nullable|email|max:150',
            'activo' => 'sometimes|boolean',
        ];
    }

    /**
     * Documentacion: Define mensajes personalizados de validacion.
     * Como lo hace: Mapea reglas a textos claros que el frontend muestra al usuario.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es requerido.',
            'nombre.min' => 'El nombre debe tener al menos 2 caracteres.',
            'apellido.required' => 'El apellido es requerido.',
            'apellido.min' => 'El apellido debe tener al menos 2 caracteres.',
            'especialidad.required' => 'La especialidad es requerida.',
            'especialidad.min' => 'La especialidad debe tener al menos 2 caracteres.',
            'telefono.regex' => 'El formato del telefono no es valido.',
            'email.email' => 'El correo electronico no es valido.',
        ];
    }
}
