<?php

/**
 * Documentacion de archivo:
 * Migracion de base de datos; crea, modifica o revierte tablas y vistas necesarias para el sistema odontologico.
 *
 * Mantiene la documentacion dentro del codigo para facilitar el estudio y mantenimiento del proyecto.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Documentacion de clase:
 * Migracion de base de datos; crea, modifica o revierte tablas y vistas necesarias para el sistema odontologico.
 */
return new class extends Migration
{
    /**
     * Documentacion: Aplica los cambios de la migracion.
     * Como lo hace: Crea o modifica estructuras de base de datos necesarias para avanzar de version.
     */
    public function up(): void
    {
        Schema::create('odontograma_condiciones', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 40)->unique();
            $table->string('label', 100);
            $table->string('color', 20)->default('#94a3b8');
            $table->string('grupo', 40)->default('hallazgo');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('grupo');
            $table->index('activo');
        });

        DB::table('odontograma_condiciones')->insert([
            ['clave' => 'sano', 'label' => 'Sano / limpiar', 'color' => '#ffffff', 'grupo' => 'neutro', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'cariado', 'label' => 'Caries', 'color' => '#ef4444', 'grupo' => 'cpo_c', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'obturado', 'label' => 'Obturado', 'color' => '#2563eb', 'grupo' => 'cpo_o', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'extraccion_indicada', 'label' => 'Extraccion indicada', 'color' => '#dc2626', 'grupo' => 'cpo_p', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'perdido', 'label' => 'Perdido / ausente', 'color' => '#374151', 'grupo' => 'cpo_p', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'endodoncia', 'label' => 'Endodoncia', 'color' => '#7c3aed', 'grupo' => 'tratamiento', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'corona', 'label' => 'Corona', 'color' => '#d4a017', 'grupo' => 'tratamiento', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'puente', 'label' => 'Puente', 'color' => '#f97316', 'grupo' => 'tratamiento', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'implante', 'label' => 'Implante', 'color' => '#0f766e', 'grupo' => 'tratamiento', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'sellante', 'label' => 'Sellante', 'color' => '#22c55e', 'grupo' => 'tratamiento', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'fractura', 'label' => 'Fractura', 'color' => '#b91c1c', 'grupo' => 'hallazgo', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ['clave' => 'tratamiento_indicado', 'label' => 'Tratamiento indicado', 'color' => '#facc15', 'grupo' => 'hallazgo', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Documentacion: Revierte los cambios de la migracion.
     * Como lo hace: Elimina o restaura estructuras para regresar al estado anterior.
     */
    public function down(): void
    {
        Schema::dropIfExists('odontograma_condiciones');
    }
};
