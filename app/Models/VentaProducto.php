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
class VentaProducto extends Model
{
    use HasFactory;

    protected $table = 'ventas_producto';

    protected $fillable = [
        'paciente_id',
        'usuario_id',
        'total',
        'monto_pagado',
        'saldo_pendiente',
        'estado',
        'metodo_pago',
        'referencia',
        'observaciones',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'total' => 'float',
        'monto_pagado' => 'float',
        'saldo_pendiente' => 'float',
        'created_at' => 'datetime',
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

    /**
     * Documentacion: Formatea el nombre de un usuario.
     * Como lo hace: Une nombre y apellido o devuelve texto de usuario no identificado.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Documentacion: Ejecuta la operacion detalles.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }

    /**
     * Documentacion: Ejecuta la operacion abonos.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function abonos()
    {
        return $this->hasMany(AbonoVentaProducto::class, 'venta_id');
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
        return $query->whereBetween('created_at', [$inicio, $fin]);
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

    /**
     * Documentacion: Ejecuta la operacion calcular total.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function calcularTotal(): float
    {
        return $this->detalles()->sum('subtotal');
    }

    /**
     * Documentacion: Ejecuta la operacion cantidad productos.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function cantidadProductos(): int
    {
        return $this->detalles()->count();
    }
}
