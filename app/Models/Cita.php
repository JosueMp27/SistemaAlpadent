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
class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';

    protected $fillable = [
        'paciente_id',
        'usuario_id',
        'tipo_tratamiento_id',
        'doctor_externo_id',
        'fecha_hora_inicio',
        'motivo_consulta',
        'estado',
        'observaciones',
        'es_primera_vez',
    ];

    protected $casts = [
        'fecha_hora_inicio' => 'datetime:Y-m-d H:i:s',
        'es_primera_vez'    => 'boolean',
        'created_at'        => 'datetime:Y-m-d H:i:s',
        'updated_at'        => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * Documentacion: Ejecuta la operacion serialize date.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

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
     * Documentacion: Ejecuta la operacion doctor externo.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function doctorExterno()
    {
        return $this->belongsTo(DoctorExterno::class, 'doctor_externo_id');
    }

    /**
     * Documentacion: Ejecuta la operacion tipo tratamiento.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function tipoTratamiento()
    {
        return $this->belongsTo(TipoTratamiento::class, 'tipo_tratamiento_id');
    }

    /**
     * Documentacion: Ejecuta la operacion diagnostico.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function diagnostico()
    {
        return $this->hasOne(Diagnostico::class, 'cita_id');
    }

    /**
     * Documentacion: Ejecuta la operacion tratamientos.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function tratamientos()
    {
        return $this->hasMany(TratamientoRealizado::class, 'cita_id');
    }

    /**
     * Documentacion: Ejecuta la operacion pago.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function pago()
    {
        return $this->hasOne(Pago::class, 'cita_id');
    }

    /**
     * Documentacion: Ejecuta la operacion recordatorio.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function recordatorio()
    {
        return $this->hasOne(Recordatorio::class, 'cita_id');
    }

    // Scopes
    /**
     * Documentacion: Ejecuta la operacion scope proximamente.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function scopeProximamente($query)
    {
        return $query->where('estado', 'programada')
                     ->where('fecha_hora_inicio', '>', now());
    }

    /**
     * Documentacion: Ejecuta la operacion scope completadas.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    /**
     * Documentacion: Ejecuta la operacion scope canceladas.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function scopeCanceladas($query)
    {
        return $query->where('estado', 'cancelada');
    }

    /**
     * Documentacion: Ejecuta la operacion scope hoy.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function scopeHoy($query)
    {
        return $query->whereDate('fecha_hora_inicio', today());
    }

    // Métodos
    /**
     * Documentacion: Ejecuta la operacion esta en curso.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function estaEnCurso(): bool
    {
        return $this->estado === 'en_curso';
    }

    /**
     * Documentacion: Ejecuta la operacion esta programada.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function estaProgramada(): bool
    {
        return $this->estado === 'programada';
    }

    /**
     * Documentacion: Ejecuta la operacion esta completada.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function estaCompletada(): bool
    {
        return $this->estado === 'completada';
    }

    /**
     * Documentacion: Ejecuta la operacion calcular total tratamientos.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function calcularTotalTratamientos(): float
    {
        return $this->tratamientos()->sum('precio_aplicado');
    }

    /**
     * Documentacion: Genera un reporte solicitado.
     * Como lo hace: Valida el tipo, calcula filas y resumen, y agrega metadatos de usuario y fecha.
     */
    public function obtenerEstadosPosibles(): array
    {
        return ['programada', 'en_curso', 'completada', 'cancelada', 'no_asistio'];
    }
}
