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
    private bool $hadCitasCompleto = false;
    private bool $hadCitasHoy = false;

    /**
     * Run the migrations.
     */
    /**
     * Documentacion: Aplica los cambios de la migracion.
     * Como lo hace: Crea o modifica estructuras de base de datos necesarias para avanzar de version.
     */
    public function up(): void
    {
        $this->hadCitasCompleto = $this->viewExists('v_citas_completo');
        $this->hadCitasHoy = $this->viewExists('v_citas_hoy');

        $this->dropDependentViews();

        Schema::table('citas', function (Blueprint $table) {
            if (! Schema::hasColumn('citas', 'tipo_tratamiento_id')) {
                $table->foreignId('tipo_tratamiento_id')
                    ->nullable()
                    ->after('usuario_id')
                    ->constrained('tipos_tratamiento')
                    ->restrictOnDelete();
            }
        });

        if (Schema::hasColumn('citas', 'fecha_hora_fin')) {
            Schema::table('citas', function (Blueprint $table) {
                $table->dropColumn('fecha_hora_fin');
            });
        }

        if ($this->hadCitasCompleto) {
            $this->createCitasCompletoView();
        }

        if ($this->hadCitasHoy) {
            $this->createCitasHoyView();
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
        $this->dropDependentViews();

        Schema::table('citas', function (Blueprint $table) {
            if (! Schema::hasColumn('citas', 'fecha_hora_fin')) {
                $table->dateTime('fecha_hora_fin')->nullable()->after('fecha_hora_inicio');
            }
        });

        if (Schema::hasColumn('citas', 'tipo_tratamiento_id')) {
            Schema::table('citas', function (Blueprint $table) {
                $table->dropConstrainedForeignId('tipo_tratamiento_id');
            });
        }
    }

    /**
     * Documentacion: Ejecuta la operacion view exists.
     * Como lo hace: Migracion de base de datos; crea, modifica o revierte tablas y vistas necesarias para el sistema odontologico.
     */
    private function viewExists(string $view): bool
    {
        return DB::table('information_schema.VIEWS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $view)
            ->exists();
    }

    /**
     * Documentacion: Ejecuta la operacion drop dependent views.
     * Como lo hace: Migracion de base de datos; crea, modifica o revierte tablas y vistas necesarias para el sistema odontologico.
     */
    private function dropDependentViews(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_citas_completo');
        DB::statement('DROP VIEW IF EXISTS v_citas_hoy');
    }

    /**
     * Documentacion: Ejecuta la operacion create citas completo view.
     * Como lo hace: Migracion de base de datos; crea, modifica o revierte tablas y vistas necesarias para el sistema odontologico.
     */
    private function createCitasCompletoView(): void
    {
        DB::statement(<<<'SQL'
            CREATE VIEW v_citas_completo AS
            SELECT
                c.id AS cita_id,
                c.fecha_hora_inicio AS fecha_hora_inicio,
                c.tipo_tratamiento_id AS tipo_tratamiento_id,
                tt.nombre AS tipo_tratamiento,
                tt.precio AS precio_tratamiento,
                c.motivo_consulta AS motivo_consulta,
                c.estado AS estado,
                fn_descripcion_estado_cita(c.estado) AS estado_descripcion,
                c.observaciones AS observaciones,
                c.es_primera_vez AS es_primera_vez,
                p.id AS paciente_id,
                p.numero_historia AS numero_historia,
                CONCAT(TRIM(p.nombre), ' ', TRIM(p.apellido)) AS paciente_nombre,
                fn_calcular_edad(p.fecha_nacimiento) AS paciente_edad,
                p.telefono AS paciente_telefono,
                p.es_menor AS es_menor,
                u.id AS usuario_id,
                CONCAT(TRIM(u.nombre), ' ', TRIM(u.apellido)) AS registrado_por,
                u.rol AS rol_usuario,
                de.id AS doctor_externo_id,
                CONCAT(TRIM(de.nombre), ' ', TRIM(de.apellido)) AS doctor_externo_nombre,
                de.especialidad AS especialidad,
                fn_cita_tiene_diagnostico(c.id) AS tiene_diagnostico,
                fn_cita_tiene_pago(c.id) AS tiene_pago,
                fn_total_tratamientos_cita(c.id) AS total_tratamientos,
                c.created_at AS created_at
            FROM citas c
            JOIN pacientes p ON p.id = c.paciente_id
            JOIN usuarios u ON u.id = c.usuario_id
            LEFT JOIN tipos_tratamiento tt ON tt.id = c.tipo_tratamiento_id
            LEFT JOIN doctores_externos de ON de.id = c.doctor_externo_id
        SQL);
    }

    /**
     * Documentacion: Ejecuta la operacion create citas hoy view.
     * Como lo hace: Migracion de base de datos; crea, modifica o revierte tablas y vistas necesarias para el sistema odontologico.
     */
    private function createCitasHoyView(): void
    {
        DB::statement(<<<'SQL'
            CREATE VIEW v_citas_hoy AS
            SELECT
                c.id AS cita_id,
                c.fecha_hora_inicio AS fecha_hora_inicio,
                CAST(c.fecha_hora_inicio AS TIME) AS hora_inicio,
                c.tipo_tratamiento_id AS tipo_tratamiento_id,
                tt.nombre AS tipo_tratamiento,
                tt.precio AS precio_tratamiento,
                c.motivo_consulta AS motivo_consulta,
                c.estado AS estado,
                fn_descripcion_estado_cita(c.estado) AS estado_descripcion,
                c.es_primera_vez AS es_primera_vez,
                p.id AS paciente_id,
                p.numero_historia AS numero_historia,
                CONCAT(TRIM(p.nombre), ' ', TRIM(p.apellido)) AS paciente_nombre,
                p.telefono AS paciente_telefono,
                fn_calcular_edad(p.fecha_nacimiento) AS paciente_edad,
                p.es_menor AS es_menor,
                de.especialidad AS especialidad_doctor,
                CONCAT(TRIM(de.nombre), ' ', TRIM(de.apellido)) AS doctor_externo,
                fn_cita_tiene_diagnostico(c.id) AS tiene_diagnostico,
                fn_cita_tiene_pago(c.id) AS tiene_pago
            FROM citas c
            JOIN pacientes p ON p.id = c.paciente_id
            LEFT JOIN tipos_tratamiento tt ON tt.id = c.tipo_tratamiento_id
            LEFT JOIN doctores_externos de ON de.id = c.doctor_externo_id
            WHERE CAST(c.fecha_hora_inicio AS DATE) = CURDATE()
                AND c.estado NOT IN ('cancelada', 'no_asistio')
            ORDER BY c.fecha_hora_inicio
        SQL);
    }
};
