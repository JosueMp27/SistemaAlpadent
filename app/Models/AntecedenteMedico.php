<?php

/**
 * Documentacion de archivo:
 * Modelo Eloquent que representa una tabla, define campos editables, casts, relaciones, scopes y metodos de dominio.
 *
 * Mantiene la documentacion dentro del codigo para facilitar el estudio y mantenimiento del proyecto.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Documentacion de clase:
 * Modelo Eloquent que representa una tabla, define campos editables, casts, relaciones, scopes y metodos de dominio.
 */
class AntecedenteMedico extends Model
{
    use HasFactory;

    protected $table = 'antecedentes_medicos';

    protected $fillable = [
        'paciente_id',
        'bajo_tratamiento_medico',
        'diabetes',
        'alergias_medicamentos',
        'detalle_alergias',
        'problemas_hemorragicos',
        'hipertenso',
        'embarazo',
        'motivo_consulta_inicial',
        'problemas_cardiacos',
        'problemas_renales',
        'otros',
        'presion_arterial',
    ];

    protected $casts = [
        'diabetes' => 'boolean',
        'alergias_medicamentos' => 'boolean',
        'problemas_hemorragicos' => 'boolean',
        'problemas_cardiacos' => 'boolean',
        'problemas_renales' => 'boolean',
        'embarazo' => 'boolean',
        'bajo_tratamiento_medico' => 'boolean',
        'hipertenso' => 'boolean',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    /**
     * Documentacion: Formatea el nombre de un paciente.
     * Como lo hace: Une nombre y apellido o devuelve texto seguro cuando falta la relacion.
     */
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    // Métodos
    /**
     * Documentacion: Ejecuta la operacion tiene problemas de contrain.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function tieneProblemasDeContrain(): bool
    {
        return $this->diabetes || 
               $this->alergias_medicamentos || 
               $this->problemas_hemorragicos || 
               $this->problemas_cardiacos || 
               $this->problemas_renales || 
               $this->embarazo;
    }
}
