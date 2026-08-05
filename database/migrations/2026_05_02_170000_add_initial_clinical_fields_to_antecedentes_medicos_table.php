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
        if (! Schema::hasColumn('antecedentes_medicos', 'bajo_tratamiento_medico')) {
            Schema::table('antecedentes_medicos', function (Blueprint $table) {
                $table->boolean('bajo_tratamiento_medico')->default(false);
            });
        }

        if (! Schema::hasColumn('antecedentes_medicos', 'hipertenso')) {
            Schema::table('antecedentes_medicos', function (Blueprint $table) {
                $table->boolean('hipertenso')->default(false);
            });
        }

        if (! Schema::hasColumn('antecedentes_medicos', 'motivo_consulta_inicial')) {
            Schema::table('antecedentes_medicos', function (Blueprint $table) {
                $table->string('motivo_consulta_inicial', 255)->nullable();
            });
        }
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
        $columns = array_filter([
            Schema::hasColumn('antecedentes_medicos', 'bajo_tratamiento_medico') ? 'bajo_tratamiento_medico' : null,
            Schema::hasColumn('antecedentes_medicos', 'hipertenso') ? 'hipertenso' : null,
            Schema::hasColumn('antecedentes_medicos', 'motivo_consulta_inicial') ? 'motivo_consulta_inicial' : null,
        ]);

        if ($columns !== []) {
            Schema::table('antecedentes_medicos', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
