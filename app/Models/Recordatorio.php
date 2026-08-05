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
class Recordatorio extends Model
{
    use HasFactory;

    protected $table = 'recordatorios';

    public $timestamps = false;

    protected $fillable = [
        'cita_id',
        'canal',
        'estado',
        'fecha_envio',
        'mensaje',
        'created_at',
    ];

    protected $casts = [
        'fecha_envio' => 'datetime',
        'created_at' => 'datetime',
    ];

    // Relaciones
    /**
     * Documentacion: Ejecuta la operacion cita.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function cita()
    {
        return $this->belongsTo(Cita::class, 'cita_id');
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
     * Documentacion: Ejecuta la operacion scope enviados.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function scopeEnviados($query)
    {
        return $query->where('estado', 'enviado');
    }

    /**
     * Documentacion: Ejecuta la operacion scope fallidos.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function scopeFallidos($query)
    {
        return $query->where('estado', 'fallido');
    }

    // Métodos
    /**
     * Documentacion: Ejecuta la operacion marcar enviado.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function marcarEnviado()
    {
        $this->estado = 'enviado';
        $this->fecha_envio = now();
        return $this->save();
    }

    /**
     * Documentacion: Ejecuta la operacion marcar fallido.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function marcarFallido()
    {
        $this->estado = 'fallido';
        return $this->save();
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
     * Documentacion: Ejecuta la operacion esta enviado.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function estaEnviado(): bool
    {
        return $this->estado === 'enviado';
    }

    /**
     * Documentacion: Ejecuta la operacion es fallido.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function esFallido(): bool
    {
        return $this->estado === 'fallido';
    }

    /**
     * Documentacion: Genera un reporte solicitado.
     * Como lo hace: Valida el tipo, calcula filas y resumen, y agrega metadatos de usuario y fecha.
     */
    public function obtenerCanalesValidos(): array
    {
        return ['email', 'sms', 'whatsapp'];
    }
}
