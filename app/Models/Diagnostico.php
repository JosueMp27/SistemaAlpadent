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
class Diagnostico extends Model
{
    use HasFactory;

    protected $table = 'diagnosticos';

    protected $fillable = [
        'cita_id',
        'usuario_id',
        'descripcion',
        'indice_cpo_cariados',
        'indice_cpo_perdidos',
        'indice_cpo_obturados',
        'gingivitis',
        'enfermedad_periodontal',
    ];

    protected $casts = [
        'gingivitis' => 'boolean',
        'enfermedad_periodontal' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    /**
     * Documentacion: Ejecuta la operacion cita.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function cita()
    {
        return $this->belongsTo(Cita::class, 'cita_id');
    }

    /**
     * Documentacion: Formatea el nombre de un usuario.
     * Como lo hace: Une nombre y apellido o devuelve texto de usuario no identificado.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Documentacion: Ejecuta la operacion dientes.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function dientes()
    {
        return $this->hasMany(DienteDiagnostico::class, 'diagnostico_id');
    }

    /**
     * Documentacion: Ejecuta la operacion tratamientos.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function tratamientos()
    {
        return $this->hasMany(TratamientoRealizado::class, 'diagnostico_id');
    }

    // Métodos
    /**
     * Documentacion: Ejecuta la operacion calcular indice cpo.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function calcularIndiceCPO(): int
    {
        return $this->indice_cpo_cariados + $this->indice_cpo_perdidos + $this->indice_cpo_obturados;
    }

    /**
     * Documentacion: Ejecuta la operacion tiene enfermedad periodontal.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function tieneEnfermedadPeriodontal(): bool
    {
        return $this->gingivitis || $this->enfermedad_periodontal;
    }

    /**
     * Documentacion: Construye el odontograma de un diagnostico.
     * Como lo hace: Agrupa dientes por numero y agrega resumen de indices CPO y hallazgos periodontales.
     */
    public function obtenerOdontograma()
    {
        return $this->dientes()->get()->groupBy('numero_diente');
    }
}
