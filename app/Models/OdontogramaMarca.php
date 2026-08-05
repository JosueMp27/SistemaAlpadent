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
class OdontogramaMarca extends Model
{
    use HasFactory;

    protected $table = 'odontograma_marcas';

    protected $fillable = [
        'odontograma_id',
        'cita_id',
        'tipo_tratamiento_id',
        'usuario_id',
        'numero_diente',
        'denticion',
        'superficie',
        'condicion',
        'color',
        'observacion',
    ];

    protected $casts = [
        'numero_diente' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Documentacion: Ejecuta la operacion odontograma.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function odontograma()
    {
        return $this->belongsTo(Odontograma::class, 'odontograma_id');
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
     * Documentacion: Ejecuta la operacion tipo tratamiento.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function tipoTratamiento()
    {
        return $this->belongsTo(TipoTratamiento::class, 'tipo_tratamiento_id');
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
