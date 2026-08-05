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
class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'marca',
        'descripcion',
        'precio_venta',
        'stock_actual',
        'stock_minimo',
        'activo',
    ];

    protected $casts = [
        'precio_venta' => 'float',
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    /**
     * Documentacion: Ejecuta la operacion detalle ventas.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'producto_id');
    }

    /**
     * Documentacion: Ejecuta la operacion movimientos inventario.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function movimientosInventario()
    {
        return $this->hasMany(MovimientoInventario::class, 'producto_id');
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
     * Documentacion: Ejecuta la operacion scope stock bajo.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function scopeStockBajo($query)
    {
        return $query->whereRaw('stock_actual <= stock_minimo');
    }

    /**
     * Documentacion: Ejecuta la operacion scope sin stock.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function scopeSinStock($query)
    {
        return $query->where('stock_actual', 0);
    }

    // Métodos
    /**
     * Documentacion: Ejecuta la operacion tiene stock bajo.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function tieneStockBajo(): bool
    {
        return $this->stock_actual <= $this->stock_minimo;
    }

    /**
     * Documentacion: Ejecuta la operacion tiene stock.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function tieneStock(): bool
    {
        return $this->stock_actual > 0;
    }

    /**
     * Documentacion: Ejecuta la operacion calcular unidades faltantes.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function calcularUnidadesFaltantes(): int
    {
        return max(0, $this->stock_minimo - $this->stock_actual);
    }

    /**
     * Documentacion: Ejecuta la operacion descontar.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function descontar($cantidad): bool
    {
        if ($this->stock_actual < $cantidad) {
            return false;
        }

        $this->stock_actual -= $cantidad;
        return $this->save();
    }

    /**
     * Documentacion: Ejecuta la operacion agregar.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function agregar($cantidad): bool
    {
        $this->stock_actual += $cantidad;
        return $this->save();
    }
}
