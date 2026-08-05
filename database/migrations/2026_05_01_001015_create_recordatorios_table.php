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
        Schema::create('recordatorios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cita_id')->constrained('citas')->cascadeOnDelete();
            $table->enum('canal', ['email', 'sms', 'whatsapp'])->default('email');
            $table->enum('estado', ['pendiente', 'enviado', 'fallido'])->default('pendiente');
            $table->timestamp('fecha_envio')->nullable();
            $table->text('mensaje')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('cita_id');
            $table->index('estado');
            $table->index('fecha_envio');
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
        Schema::dropIfExists('recordatorios');
    }
};
