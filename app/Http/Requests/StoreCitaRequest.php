<?php

/**
 * Documentacion de archivo:
 * FormRequest que normaliza datos de entrada y aplica reglas de validacion antes de llegar al controlador.
 *
 * Mantiene la documentacion dentro del codigo para facilitar el estudio y mantenimiento del proyecto.
 */

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Documentacion de clase:
 * FormRequest que normaliza datos de entrada y aplica reglas de validacion antes de llegar al controlador.
 */
class StoreCitaRequest extends FormRequest
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
            'motivo_consulta' => $this->motivo_consulta ? trim($this->motivo_consulta) : null,
            'observaciones' => $this->observaciones ? trim($this->observaciones) : null,
        ]);
    }

    /**
     * Documentacion: Define reglas de validacion.
     * Como lo hace: Devuelve el arreglo de reglas que Laravel aplica al request.
     */
    public function rules(): array
    {
        return [
            'paciente_id' => [
                'required',
                'integer',
                Rule::exists('pacientes', 'id')->where(function ($query) {
                    $query->where('activo', 1);
                }),
            ],

            'usuario_id' => [
                'required',
                'integer',
                Rule::exists('usuarios', 'id')->where(function ($query) {
                    $query->where('activo', 1);
                }),
            ],

            'tipo_tratamiento_id' => [
                'required',
                'integer',
                Rule::exists('tipos_tratamiento', 'id')->where(function ($query) {
                    $query->where('activo', 1);
                }),
            ],

            'doctor_externo_id' => [
                'nullable',
                'integer',
                Rule::exists('doctores_externos', 'id')->where(function ($query) {
                    $query->where('activo', 1);
                }),
            ],

            'fecha_hora_inicio' => 'required|date',
            'motivo_consulta' => 'required|string|min:3|max:255',
            'observaciones' => 'nullable|string|max:500',
        ];
    }

    /**
     * Documentacion: Agrega validaciones posteriores.
     * Como lo hace: Revisa reglas que dependen de fechas, horarios o condiciones calculadas.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->filled('fecha_hora_inicio')) {
                return;
            }

            try {
                $timezone = config('app.timezone', 'America/Guayaquil');

                $inicio = Carbon::createFromFormat('Y-m-d H:i:s', $this->fecha_hora_inicio, $timezone);
                $ahora = Carbon::now($timezone);

                if ($inicio->lessThanOrEqualTo($ahora)) {
                    $validator->errors()->add(
                        'fecha_hora_inicio',
                        'La cita debe programarse en un horario futuro.'
                    );
                }

                $horaInicio = $inicio->format('H:i');

                if ($horaInicio < '09:00' || $horaInicio >= '18:00') {
                    $validator->errors()->add(
                        'fecha_hora_inicio',
                        'La hora de inicio debe estar dentro del horario de atencion: 09:00 a 18:00.'
                    );
                }
            } catch (\Exception $e) {
                $validator->errors()->add(
                    'fecha_hora_inicio',
                    'El formato de la fecha u hora no es valido.'
                );
            }
        });
    }

    /**
     * Documentacion: Define mensajes personalizados de validacion.
     * Como lo hace: Mapea reglas a textos claros que el frontend muestra al usuario.
     */
    public function messages(): array
    {
        return [
            'paciente_id.required' => 'Debe seleccionar un paciente.',
            'paciente_id.integer' => 'El paciente seleccionado no es valido.',
            'paciente_id.exists' => 'El paciente seleccionado no existe o esta inactivo.',

            'usuario_id.required' => 'No se pudo identificar al usuario que registra la cita.',
            'usuario_id.integer' => 'El usuario seleccionado no es valido.',
            'usuario_id.exists' => 'El usuario seleccionado no existe o esta inactivo.',

            'tipo_tratamiento_id.required' => 'Debe seleccionar un tratamiento.',
            'tipo_tratamiento_id.integer' => 'El tratamiento seleccionado no es valido.',
            'tipo_tratamiento_id.exists' => 'El tratamiento seleccionado no existe o esta inactivo.',

            'doctor_externo_id.integer' => 'El doctor externo seleccionado no es valido.',
            'doctor_externo_id.exists' => 'El doctor externo seleccionado no existe o esta inactivo.',

            'fecha_hora_inicio.required' => 'La fecha y hora de inicio es requerida.',
            'fecha_hora_inicio.date' => 'La fecha y hora de inicio no tiene un formato valido.',

            'motivo_consulta.required' => 'El motivo de consulta es requerido.',
            'motivo_consulta.min' => 'El motivo de consulta debe tener al menos 3 caracteres.',
            'motivo_consulta.max' => 'El motivo de consulta no puede superar los 255 caracteres.',

            'observaciones.max' => 'Las observaciones no pueden superar los 500 caracteres.',
        ];
    }
}
