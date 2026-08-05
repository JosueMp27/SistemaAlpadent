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
        Schema::create('dientes_diagnostico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('diagnostico_id')->constrained('diagnosticos')->cascadeOnDelete();
            $table->unsignedTinyInteger('numero_diente');
            $table->enum('condicion', [
                'sano',
                'cariado',
                'obturado',
                'faltante',
                'con_tratamiento_radicular',
                'con_corona',
                'con_puente',
                'implante',
                'ausente'
            ]);
            $table->set('superficie', ['oclusal', 'vestibular', 'lingual', 'mesial', 'distal'])->nullable();
            $table->text('observacion')->nullable();

            $table->index('diagnostico_id');
            $table->index('numero_diente');
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
        Schema::dropIfExists('dientes_diagnostico');
    }
};
