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
class TipoTratamiento extends Model
{
    use HasFactory;

    protected $table = 'tipos_tratamiento';

    protected $fillable = [
        'nombre',
        'categoria',
        'precio',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'precio' => 'float',
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    /**
     * Documentacion: Ejecuta la operacion tratamientos realizados.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function tratamientosRealizados()
    {
        return $this->hasMany(TratamientoRealizado::class, 'tipo_tratamiento_id');
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

    /**
     * Documentacion: Ejecuta la operacion scope por categoria.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    // Métodos
    /**
     * Documentacion: Obtiene categorias disponibles.
     * Como lo hace: Extrae categorias distintas de tratamientos activos y las ordena.
     */
    public function obtenerCategoriasValidas(): array
    {
        return [
            'operatoria',
            'periodoncia',
            'protesis_removible',
            'protesis_fija',
            'exodoncia',
            'ortodoncia',
            'endodoncia',
            'rayos_x',
            'cirugia',
            'limpieza',
            'otros'
        ];
    }
}
