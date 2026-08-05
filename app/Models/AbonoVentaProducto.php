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
class AbonoVentaProducto extends Model
{
    use HasFactory;

    protected $table = 'abonos_venta_producto';

    public $timestamps = false;

    protected $fillable = [
        'venta_id',
        'usuario_id',
        'monto',
        'metodo_pago',
        'referencia',
        'observaciones',
        'fecha',
    ];

    protected $casts = [
        'monto' => 'float',
        'fecha' => 'datetime',
    ];

    /**
     * Documentacion: Ejecuta la operacion venta.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function venta()
    {
        return $this->belongsTo(VentaProducto::class, 'venta_id');
    }

    /**
     * Documentacion: Formatea el nombre de un usuario.
     * Como lo hace: Une nombre y apellido o devuelve texto de usuario no identificado.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
