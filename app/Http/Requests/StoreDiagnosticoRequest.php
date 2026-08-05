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
class StoreDiagnosticoRequest extends FormRequest
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
            'usuario_id' => 'required|exists:usuarios,id',
            'descripcion' => 'required|string|min:5',
            'indice_cpo_cariados' => 'nullable|integer|min:0|max:32',
            'indice_cpo_perdidos' => 'nullable|integer|min:0|max:32',
            'indice_cpo_obturados' => 'nullable|integer|min:0|max:32',
            'gingivitis' => 'boolean',
            'enfermedad_periodontal' => 'boolean',
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
            'usuario_id.required' => 'El usuario es requerido',
            'descripcion.required' => 'La descripción es requerida',
            'descripcion.min' => 'La descripción debe tener al menos 5 caracteres',
            'indice_cpo_cariados.max' => 'El índice no puede ser mayor a 32',
            'indice_cpo_perdidos.max' => 'El índice no puede ser mayor a 32',
            'indice_cpo_obturados.max' => 'El índice no puede ser mayor a 32',
        ];
    }
}
