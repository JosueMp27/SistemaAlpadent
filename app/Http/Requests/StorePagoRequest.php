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
class StorePagoRequest extends FormRequest
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
            'paciente_id' => 'required|exists:pacientes,id',
            'cita_id' => 'required|exists:citas,id',
            'usuario_id' => 'required|exists:usuarios,id',
            'monto_total' => 'required|numeric|min:0.01',
            'monto_pagado' => 'nullable|numeric|min:0|lte:monto_total',
            'metodo_pago' => 'required|in:efectivo,transferencia,tarjeta',
            'referencia_transferencia' => 'required_if:metodo_pago,transferencia|nullable|string|max:100',
        ];
    }

    /**
     * Documentacion: Define mensajes personalizados de validacion.
     * Como lo hace: Mapea reglas a textos claros que el frontend muestra al usuario.
     */
    public function messages(): array
    {
        return [
            'monto_total.required' => 'El monto total es requerido',
            'monto_total.min' => 'El monto debe ser mayor a 0',
            'monto_pagado.lte' => 'El monto pagado no puede superar el total',
            'metodo_pago.required' => 'El método de pago es requerido',
            'referencia_transferencia.required_if' => 'La referencia de transferencia es requerida para transferencias',
        ];
    }
}
