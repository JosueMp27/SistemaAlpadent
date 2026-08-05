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
class TratamientoRealizado extends Model
{
    use HasFactory;

    protected $table = 'tratamientos_realizados';

    public $timestamps = false;

    protected $fillable = [
        'cita_id',
        'tipo_tratamiento_id',
        'diagnostico_id',
        'numero_diente',
        'precio_aplicado',
        'notas',
        'created_at',
    ];

    protected $casts = [
        'precio_aplicado' => 'float',
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
        return $this->belongsTo(Diagnostico::class, 'diagnostico_id');
    }

    // Métodos
    /**
     * Documentacion: Genera un reporte solicitado.
     * Como lo hace: Valida el tipo, calcula filas y resumen, y agrega metadatos de usuario y fecha.
     */
    public function obtenerNombreDiente(): string
    {
        if (!$this->numero_diente) {
            return 'N/A';
        }

        $diente = new DienteDiagnostico(['numero_diente' => $this->numero_diente]);
        return $diente->obtenerNombreDiente();
    }
}
