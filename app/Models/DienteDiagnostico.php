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
class DienteDiagnostico extends Model
{
    use HasFactory;

    protected $table = 'dientes_diagnostico';

    public $timestamps = false;

    protected $fillable = [
        'diagnostico_id',
        'numero_diente',
        'condicion',
        'superficie',
        'observacion',
    ];

    // Relaciones
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
        $dientes = [
            11 => 'Incisivo Central Superior Der',
            12 => 'Incisivo Lateral Superior Der',
            13 => 'Canino Superior Der',
            14 => 'Primer Premolar Superior Der',
            15 => 'Segundo Premolar Superior Der',
            16 => 'Primer Molar Superior Der',
            17 => 'Segundo Molar Superior Der',
            18 => 'Tercer Molar Superior Der',
            21 => 'Incisivo Central Superior Izq',
            22 => 'Incisivo Lateral Superior Izq',
            23 => 'Canino Superior Izq',
            24 => 'Primer Premolar Superior Izq',
            25 => 'Segundo Premolar Superior Izq',
            26 => 'Primer Molar Superior Izq',
            27 => 'Segundo Molar Superior Izq',
            28 => 'Tercer Molar Superior Izq',
            31 => 'Incisivo Central Inferior Izq',
            32 => 'Incisivo Lateral Inferior Izq',
            33 => 'Canino Inferior Izq',
            34 => 'Primer Premolar Inferior Izq',
            35 => 'Segundo Premolar Inferior Izq',
            36 => 'Primer Molar Inferior Izq',
            37 => 'Segundo Molar Inferior Izq',
            38 => 'Tercer Molar Inferior Izq',
            41 => 'Incisivo Central Inferior Der',
            42 => 'Incisivo Lateral Inferior Der',
            43 => 'Canino Inferior Der',
            44 => 'Primer Premolar Inferior Der',
            45 => 'Segundo Premolar Inferior Der',
            46 => 'Primer Molar Inferior Der',
            47 => 'Segundo Molar Inferior Der',
            48 => 'Tercer Molar Inferior Der',
        ];
        
        return $dientes[$this->numero_diente] ?? "Diente {$this->numero_diente}";
    }

    /**
     * Documentacion: Genera un reporte solicitado.
     * Como lo hace: Valida el tipo, calcula filas y resumen, y agrega metadatos de usuario y fecha.
     */
    public function obtenerSuperficies(): array
    {
        return $this->superficie ? explode(',', $this->superficie) : [];
    }

    /**
     * Documentacion: Obtiene condiciones configurables del odontograma.
     * Como lo hace: Lee opciones desde base de datos y usa un catalogo fijo si aun no hay registros.
     */
    public function obtenerCondicionesValidas(): array
    {
        return [
            'sano',
            'cariado',
            'obturado',
            'faltante',
            'con_tratamiento_radicular',
            'con_corona',
            'con_puente',
            'implante',
            'ausente'
        ];
    }
}
