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
class DoctorExterno extends Model
{
    use HasFactory;

    protected $table = 'doctores_externos';

    protected $fillable = [
        'nombre',
        'apellido',
        'especialidad',
        'telefono',
        'email',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    /**
     * Documentacion: Ejecuta la operacion citas.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function citas()
    {
        return $this->hasMany(Cita::class, 'doctor_externo_id');
    }

    // Scopes
    /**
     * Documentacion: Ejecuta la operacion scope activos.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // Métodos
    /**
     * Documentacion: Ejecuta la operacion get nombre completo.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function getNombreCompleto(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }
}
