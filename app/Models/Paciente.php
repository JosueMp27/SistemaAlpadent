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
class Paciente extends Model
{
    use HasFactory;

    protected $table = 'pacientes';

    protected $fillable = [
        'numero_historia',
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'sexo',
        'telefono',
        'email',
        'direccion',
        'es_menor',
        'activo',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date: Y-m-d:',
        'es_menor' => 'boolean',
        'activo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    /**
     * Documentacion: Ejecuta la operacion antecedentes.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function antecedentes()
    {
        return $this->hasOne(AntecedenteMedico::class, 'paciente_id');
    }

    /**
     * Documentacion: Ejecuta la operacion citas.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function citas()
    {
        return $this->hasMany(Cita::class, 'paciente_id');
    }

    /**
     * Documentacion: Ejecuta la operacion odontograma.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function odontograma()
    {
        return $this->hasOne(Odontograma::class, 'paciente_id');
    }

    /**
     * Documentacion: Ejecuta la operacion pagos.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'paciente_id');
    }

    /**
     * Documentacion: Ejecuta la operacion ventas producto.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function ventasProducto()
    {
        return $this->hasMany(VentaProducto::class, 'paciente_id');
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
     * Documentacion: Ejecuta la operacion scope mayores.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function scopeMayores($query)
    {
        return $query->where('es_menor', false);
    }

    /**
     * Documentacion: Ejecuta la operacion scope menores.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function scopeMenores($query)
    {
        return $query->where('es_menor', true);
    }

    // Métodos
    /**
     * Documentacion: Ejecuta la operacion get nombre completo.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function getNombreCompleto(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }

    /**
     * Documentacion: Ejecuta la operacion calcular edad.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function calcularEdad(): int
    {
        return $this->fecha_nacimiento->diffInYears(now());
    }

    /**
     * Documentacion: Calcula saldo total pendiente.
     * Como lo hace: Suma saldos de pagos pendientes o parciales relacionados con el paciente.
     */
    public function obtenerSaldoTotal(): float
    {
        return $this->pagos()
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->sum('saldo_pendiente');
    }

    /**
     * Documentacion: Obtiene historial clinico o financiero relacionado.
     * Como lo hace: Carga relaciones necesarias en una sola consulta compuesta para mostrar el detalle completo.
     */
    public function obtenerHistorial()
    {
        return $this->citas()
            ->with(['diagnosticos', 'tratamientos', 'pagos'])
            ->orderBy('fecha_hora_inicio', 'desc')
            ->get();
    }
}
