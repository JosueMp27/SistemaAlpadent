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
class DetalleVenta extends Model
{
    use HasFactory;

    protected $table = 'detalle_venta';

    public $timestamps = false;

    protected $fillable = [
        'venta_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'float',
        'subtotal' => 'float',
    ];

    // Relaciones
    /**
     * Documentacion: Ejecuta la operacion venta.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function venta()
    {
        return $this->belongsTo(VentaProducto::class, 'venta_id');
    }

    /**
     * Documentacion: Ejecuta la operacion producto.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    // Métodos
    /**
     * Documentacion: Ejecuta la operacion calcular subtotal.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function calcularSubtotal(): float
    {
        return $this->cantidad * $this->precio_unitario;
    }
}
