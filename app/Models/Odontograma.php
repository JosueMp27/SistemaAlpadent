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
class Odontograma extends Model
{
    use HasFactory;

    protected $table = 'odontogramas';

    protected $fillable = [
        'paciente_id',
        'usuario_id',
        'indice_cpo_cariados',
        'indice_cpo_perdidos',
        'indice_cpo_obturados',
        'indice_ceo_cariados',
        'indice_ceo_extraidos',
        'indice_ceo_obturados',
        'higiene_placa',
        'higiene_calculo',
        'higiene_gingivitis',
        'enfermedad_periodontal',
        'maloclusion',
        'fluorosis',
        'observaciones',
    ];

    protected $casts = [
        'indice_cpo_cariados' => 'integer',
        'indice_cpo_perdidos' => 'integer',
        'indice_cpo_obturados' => 'integer',
        'indice_ceo_cariados' => 'integer',
        'indice_ceo_extraidos' => 'integer',
        'indice_ceo_obturados' => 'integer',
        'higiene_placa' => 'integer',
        'higiene_calculo' => 'integer',
        'higiene_gingivitis' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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
     * Documentacion: Ejecuta la operacion marcas.
     * Como lo hace: Usa relaciones, scopes o atributos del modelo para encapsular comportamiento de la tabla.
     */
    public function marcas()
    {
        return $this->hasMany(OdontogramaMarca::class, 'odontograma_id');
    }
}
