<?php

/**
 * Documentacion de archivo:
 * Migracion de base de datos; crea, modifica o revierte tablas y vistas necesarias para el sistema odontologico.
 *
 * Mantiene la documentacion dentro del codigo para facilitar el estudio y mantenimiento del proyecto.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Documentacion de clase:
 * Migracion de base de datos; crea, modifica o revierte tablas y vistas necesarias para el sistema odontologico.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Documentacion: Aplica los cambios de la migracion.
     * Como lo hace: Crea o modifica estructuras de base de datos necesarias para avanzar de version.
     */
    public function up(): void
    {
        Schema::create('diagnosticos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cita_id')->constrained('citas')->restrictOnDelete();
            $table->foreignId('usuario_id')->constrained('usuarios')->restrictOnDelete();
            $table->text('descripcion');
            $table->unsignedTinyInteger('indice_cpo_cariados')->default(0);
            $table->unsignedTinyInteger('indice_cpo_perdidos')->default(0);
            $table->unsignedTinyInteger('indice_cpo_obturados')->default(0);
            $table->boolean('gingivitis')->default(false);
            $table->boolean('enfermedad_periodontal')->default(false);
            $table->timestamps();

            $table->unique('cita_id');
            $table->index('cita_id');
            $table->index('usuario_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    /**
     * Documentacion: Revierte los cambios de la migracion.
     * Como lo hace: Elimina o restaura estructuras para regresar al estado anterior.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnosticos');
    }
};
