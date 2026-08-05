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
class Abono extends Model
{
    use HasFactory;

    protected $table = 'abonos';

    public $timestamps = false;

    protected $fillable = [
        'pago_id',
        'usuario_id',
        'monto',
        'metodo_pago',
        'referencia',
        'fecha',
        'observaciones',
    ];

    protected $casts = [
        'monto' => 'float',
        'fecha' => 'datetime',
    ];

    // Relaciones
    /**
     * Documentacion: Ejecuta la operacion pago.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function pago()
    {
        return $this->belongsTo(Pago::class, 'pago_id');
    }

    /**
     * Documentacion: Formatea el nombre de un usuario.
     * Como lo hace: Une nombre y apellido o devuelve texto de usuario no identificado.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Scopes
    /**
     * Documentacion: Ejecuta la operacion scope por metodo.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function scopePorMetodo($query, $metodo)
    {
        return $query->where('metodo_pago', $metodo);
    }

    /**
     * Documentacion: Ejecuta la operacion scope en rango.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function scopeEnRango($query, $inicio, $fin)
    {
        return $query->whereBetween('fecha', [$inicio, $fin]);
    }

    // Métodos
    /**
     * Documentacion: Genera un reporte solicitado.
     * Como lo hace: Valida el tipo, calcula filas y resumen, y agrega metadatos de usuario y fecha.
     */
    public function obtenerMetodosValidos(): array
    {
        return ['efectivo', 'transferencia', 'tarjeta'];
    }
}
