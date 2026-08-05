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
class StorePacienteRequest extends FormRequest
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
        $this->merge([
            'nombre' => trim((string) $this->nombre),
            'apellido' => trim((string) $this->apellido),
            'telefono' => $this->telefono !== null ? trim((string) $this->telefono) : null,
            'email' => $this->email !== null ? trim((string) $this->email) : null,
            'direccion' => $this->direccion !== null ? trim((string) $this->direccion) : null,
            'detalle_alergias' => $this->detalle_alergias !== null ? trim((string) $this->detalle_alergias) : null,
            'motivo_consulta_inicial' => $this->motivo_consulta_inicial !== null ? trim((string) $this->motivo_consulta_inicial) : null,

            'bajo_tratamiento_medico' => filter_var($this->bajo_tratamiento_medico, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'problemas_hemorragicos' => filter_var($this->problemas_hemorragicos, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'alergias_medicamentos' => filter_var($this->alergias_medicamentos, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'hipertenso' => filter_var($this->hipertenso, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'diabetes' => filter_var($this->diabetes, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'embarazo' => $this->sexo === 'M' ? false : (filter_var($this->embarazo, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false),
        ]);
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
            'fecha_nacimiento' => 'required|date|before:today',
            'sexo' => 'required|in:M,F',
            'telefono' => 'nullable|string|regex:/^[0-9+() -]{7,20}$/',
            'email' => 'nullable|email|max:150',
            'direccion' => 'nullable|string|max:255',

            'bajo_tratamiento_medico' => 'nullable|boolean',
            'problemas_hemorragicos' => 'nullable|boolean',
            'alergias_medicamentos' => 'nullable|boolean',
            'detalle_alergias' => 'nullable|string|max:1000',
            'hipertenso' => 'nullable|boolean',
            'diabetes' => 'nullable|boolean',
            'embarazo' => 'nullable|boolean',
            'motivo_consulta_inicial' => 'nullable|string|min:3|max:255',
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
            'fecha_nacimiento.required' => 'La fecha de nacimiento es requerida.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'sexo.required' => 'El sexo es requerido.',
            'sexo.in' => 'El sexo debe ser M o F.',
            'telefono.regex' => 'El formato del teléfono no es válido.',
            'email.email' => 'El email no es válido.',
            'motivo_consulta_inicial.min' => 'El motivo de consulta debe tener al menos 3 caracteres.',
        ];
    }
}
