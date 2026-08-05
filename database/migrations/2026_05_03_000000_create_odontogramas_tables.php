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
        Schema::create('odontogramas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->restrictOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->unsignedTinyInteger('indice_cpo_cariados')->default(0);
            $table->unsignedTinyInteger('indice_cpo_perdidos')->default(0);
            $table->unsignedTinyInteger('indice_cpo_obturados')->default(0);
            $table->unsignedTinyInteger('indice_ceo_cariados')->default(0);
            $table->unsignedTinyInteger('indice_ceo_extraidos')->default(0);
            $table->unsignedTinyInteger('indice_ceo_obturados')->default(0);
            $table->unsignedTinyInteger('higiene_placa')->nullable();
            $table->unsignedTinyInteger('higiene_calculo')->nullable();
            $table->unsignedTinyInteger('higiene_gingivitis')->nullable();
            $table->string('enfermedad_periodontal', 20)->default('ninguna');
            $table->string('maloclusion', 20)->default('ninguna');
            $table->string('fluorosis', 20)->default('ninguna');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique('paciente_id');
            $table->index('usuario_id');
        });

        Schema::create('odontograma_marcas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('odontograma_id')->constrained('odontogramas')->cascadeOnDelete();
            $table->foreignId('cita_id')->nullable()->constrained('citas')->nullOnDelete();
            $table->foreignId('tipo_tratamiento_id')->nullable()->constrained('tipos_tratamiento')->nullOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->unsignedTinyInteger('numero_diente');
            $table->string('denticion', 20);
            $table->string('superficie', 20)->default('general');
            $table->string('condicion', 40);
            $table->string('color', 20)->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();

            $table->unique(['odontograma_id', 'numero_diente', 'superficie'], 'odontograma_diente_superficie_unique');
            $table->index(['numero_diente', 'superficie']);
            $table->index('condicion');
        });
    }

    /**
     * Documentacion: Revierte los cambios de la migracion.
     * Como lo hace: Elimina o restaura estructuras para regresar al estado anterior.
     */
    public function down(): void
    {
        Schema::dropIfExists('odontograma_marcas');
        Schema::dropIfExists('odontogramas');
    }
};
