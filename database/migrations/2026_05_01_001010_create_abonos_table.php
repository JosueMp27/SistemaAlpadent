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
        Schema::create('abonos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pago_id')->constrained('pagos')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('usuarios')->restrictOnDelete();
            $table->decimal('monto', 10, 2);
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta'])->default('efectivo');
            $table->string('referencia', 100)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamp('fecha')->useCurrent();

            $table->index('pago_id');
            $table->index('usuario_id');
            $table->index('fecha');
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
        Schema::dropIfExists('abonos');
    }
};
