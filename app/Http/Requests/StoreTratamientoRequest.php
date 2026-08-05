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
class StoreTratamientoRequest extends FormRequest
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
     * Documentacion: Define reglas de validacion.
     * Como lo hace: Devuelve el arreglo de reglas que Laravel aplica al request.
     */
    public function rules(): array
    {
        return [
            'cita_id' => 'required|exists:citas,id',
            'tipo_tratamiento_id' => 'required|exists:tipos_tratamiento,id',
            'diagnostico_id' => 'nullable|exists:diagnosticos,id',
            'numero_diente' => 'nullable|integer',
            'precio_aplicado' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string|max:500',
        ];
    }

    /**
     * Documentacion: Define mensajes personalizados de validacion.
     * Como lo hace: Mapea reglas a textos claros que el frontend muestra al usuario.
     */
    public function messages(): array
    {
        return [
            'cita_id.required' => 'La cita es requerida',
            'cita_id.exists' => 'La cita seleccionada no existe',
            'tipo_tratamiento_id.required' => 'El tipo de tratamiento es requerido',
            'tipo_tratamiento_id.exists' => 'El tipo de tratamiento no existe',
            'precio_aplicado.numeric' => 'El precio debe ser un número',
            'precio_aplicado.min' => 'El precio no puede ser negativo',
        ];
    }
}
