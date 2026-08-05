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
     * Documentacion: Aplica los cambios de la migracion.
     * Como lo hace: Crea o modifica estructuras de base de datos necesarias para avanzar de version.
     */
    public function up(): void
    {
        Schema::table('antecedentes_medicos', function (Blueprint $table) {
            $table->boolean('bajo_tratamiento_medico')->default(false)->after('paciente_id');
            $table->boolean('hipertenso')->default(false)->after('embarazo');
            $table->string('motivo_consulta_inicial', 255)->nullable()->after('presion_arterial');
        });
    }

    /**
     * Documentacion: Revierte los cambios de la migracion.
     * Como lo hace: Elimina o restaura estructuras para regresar al estado anterior.
     */
    public function down(): void
    {
        Schema::table('antecedentes_medicos', function (Blueprint $table) {
            $table->dropColumn([
                'bajo_tratamiento_medico',
                'hipertenso',
                'motivo_consulta_inicial',
            ]);
        });
    }
};