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
class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';

    protected $fillable = [
        'paciente_id',
        'cita_id',
        'usuario_id',
        'monto_total',
        'monto_pagado',
        'saldo_pendiente',
        'metodo_pago',
        'referencia_transferencia',
        'estado',
    ];

    protected $casts = [
        'monto_total' => 'float',
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
     * Documentacion: Ejecuta la operacion abonos.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function abonos()
    {
        return $this->hasMany(Abono::class, 'pago_id');
    }

    // Scopes
    /**
     * Documentacion: Ejecuta la operacion scope pendientes.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Documentacion: Ejecuta la operacion scope parciales.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function scopeParciales($query)
    {
        return $query->where('estado', 'parcial');
    }

    /**
     * Documentacion: Ejecuta la operacion scope pagados.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function scopePagados($query)
    {
        return $query->where('estado', 'pagado');
    }

    // Métodos
    /**
     * Documentacion: Ejecuta la operacion calcular saldo.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function calcularSaldo(): float
    {
        return $this->monto_total - $this->monto_pagado;
    }

    /**
     * Documentacion: Ejecuta la operacion aplicar abono.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function aplicarAbono($monto): bool
    {
        if ($monto > $this->saldo_pendiente) {
            return false;
        }

        $this->monto_pagado += $monto;
        $this->saldo_pendiente = $this->calcularSaldo();

        if ($this->saldo_pendiente === 0.0) {
            $this->estado = 'pagado';
        } elseif ($this->saldo_pendiente < $this->monto_total) {
            $this->estado = 'parcial';
        }

        return $this->save();
    }

    /**
     * Documentacion: Ejecuta la operacion porcentaje pagado.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function porcentajePagado(): float
    {
        if ($this->monto_total === 0) {
            return 0;
        }
        return round(($this->monto_pagado / $this->monto_total) * 100, 2);
    }

    /**
     * Documentacion: Ejecuta la operacion esta pagado.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function estaPagado(): bool
    {
        return $this->estado === 'pagado';
    }

    /**
     * Documentacion: Ejecuta la operacion esta pendiente.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function estaPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }

    /**
     * Documentacion: Ejecuta la operacion esta parcial.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function estaParcial(): bool
    {
        return $this->estado === 'parcial';
    }
}
