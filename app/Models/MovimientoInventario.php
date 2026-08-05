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
class MovimientoInventario extends Model
{
    use HasFactory;

    protected $table = 'movimientos_inventario';

    public $timestamps = false;

    protected $fillable = [
        'producto_id',
        'usuario_id',
        'tipo_movimiento',
        'cantidad',
        'descripcion',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    /**
     * Documentacion: Ejecuta la operacion producto.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
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
     * Documentacion: Ejecuta la operacion scope por tipo.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_movimiento', $tipo);
    }

    /**
     * Documentacion: Ejecuta la operacion scope entradas.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function scopeEntradas($query)
    {
        return $query->where('tipo_movimiento', 'entrada');
    }

    /**
     * Documentacion: Ejecuta la operacion scope salidas.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function scopeSalidas($query)
    {
        return $query->where('tipo_movimiento', 'salida');
    }

    /**
     * Documentacion: Ejecuta la operacion scope ajustes.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function scopeAjustes($query)
    {
        return $query->where('tipo_movimiento', 'ajuste');
    }

    /**
     * Documentacion: Ejecuta la operacion scope en rango.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function scopeEnRango($query, $inicio, $fin)
    {
        return $query->whereBetween('created_at', [$inicio, $fin]);
    }

    // Métodos
    /**
     * Documentacion: Genera un reporte solicitado.
     * Como lo hace: Valida el tipo, calcula filas y resumen, y agrega metadatos de usuario y fecha.
     */
    public function obtenerTiposValidos(): array
    {
        return ['entrada', 'salida', 'ajuste'];
    }

    /**
     * Documentacion: Ejecuta la operacion es entrada.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function esEntrada(): bool
    {
        return $this->tipo_movimiento === 'entrada';
    }

    /**
     * Documentacion: Ejecuta la operacion es salida.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function esSalida(): bool
    {
        return $this->tipo_movimiento === 'salida';
    }

    /**
     * Documentacion: Ejecuta la operacion es ajuste.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function esAjuste(): bool
    {
        return $this->tipo_movimiento === 'ajuste';
    }
}
