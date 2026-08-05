<?php

/**
 * Documentacion de archivo:
 * Modelo Eloquent que representa una tabla, define campos editables, casts, relaciones, scopes y metodos de dominio.
 *
 * Mantiene la documentacion dentro del codigo para facilitar el estudio y mantenimiento del proyecto.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Documentacion de clase:
 * Modelo Eloquent que representa una tabla, define campos editables, casts, relaciones, scopes y metodos de dominio.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'password',
        'rol',
        'activo',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
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
        return $this->hasMany(Cita::class, 'usuario_id');
    }

    /**
     * Documentacion: Ejecuta la operacion diagnosticos.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function diagnosticos()
    {
        return $this->hasMany(Diagnostico::class, 'usuario_id');
    }

    /**
     * Documentacion: Ejecuta la operacion pagos.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'usuario_id');
    }

    /**
     * Documentacion: Ejecuta la operacion abonos.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function abonos()
    {
        return $this->hasMany(Abono::class, 'usuario_id');
    }

    /**
     * Documentacion: Ejecuta la operacion ventas producto.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function ventasProducto()
    {
        return $this->hasMany(VentaProducto::class, 'usuario_id');
    }

    /**
     * Documentacion: Ejecuta la operacion movimientos inventario.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function movimientosInventario()
    {
        return $this->hasMany(MovimientoInventario::class, 'usuario_id');
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
     * Documentacion: Ejecuta la operacion scope administradores.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function scopeAdministradores($query)
    {
        return $query->where('rol', 'administrador');
    }

    /**
     * Documentacion: Ejecuta la operacion scope secretarias.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function scopeSecretarias($query)
    {
        return $query->where('rol', 'secretaria');
    }

    // Métodos
    /**
     * Documentacion: Ejecuta la operacion es administrador.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function esAdministrador(): bool
    {
        return $this->rol === 'administrador';
    }

    /**
     * Documentacion: Ejecuta la operacion es secretaria.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function esSecretaria(): bool
    {
        return $this->rol === 'secretaria';
    }

    /**
     * Documentacion: Ejecuta la operacion get nombre completo.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function getNombreCompleto(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }
}
