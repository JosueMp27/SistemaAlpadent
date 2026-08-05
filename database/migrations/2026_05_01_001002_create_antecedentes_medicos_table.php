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
        Schema::create('antecedentes_medicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->boolean('diabetes')->default(false);
            $table->boolean('alergias_medicamentos')->default(false);
            $table->text('detalle_alergias')->nullable();
            $table->boolean('problemas_hemorragicos')->default(false);
            $table->boolean('problemas_cardiacos')->default(false);
            $table->boolean('problemas_renales')->default(false);
            $table->boolean('embarazo')->default(false);
            $table->text('otros')->nullable();
            $table->string('presion_arterial', 20)->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->unique('paciente_id');
            $table->index('paciente_id');
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
        Schema::dropIfExists('antecedentes_medicos');
    }
};
