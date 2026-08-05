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
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->restrictOnDelete();
            $table->foreignId('usuario_id')->constrained('usuarios')->restrictOnDelete();
            $table->foreignId('tipo_tratamiento_id')->nullable()->constrained('tipos_tratamiento')->restrictOnDelete();
            $table->foreignId('doctor_externo_id')->nullable()->constrained('doctores_externos')->restrictOnDelete();
            $table->dateTime('fecha_hora_inicio');
            $table->string('motivo_consulta', 255);
            $table->enum('estado', ['programada', 'en_curso', 'completada', 'cancelada', 'no_asistio'])->default('programada');
            $table->text('observaciones')->nullable();
            $table->boolean('es_primera_vez')->default(false);
            $table->timestamps();

            $table->index('paciente_id');
            $table->index('usuario_id');
            $table->index('tipo_tratamiento_id');
            $table->index('doctor_externo_id');
            $table->index(['fecha_hora_inicio', 'estado']);
            $table->index('estado');
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
        Schema::dropIfExists('citas');
    }
};
