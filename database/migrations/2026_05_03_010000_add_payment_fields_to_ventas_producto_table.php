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
        Schema::table('ventas_producto', function (Blueprint $table) {
            if (! Schema::hasColumn('ventas_producto', 'monto_pagado')) {
                $table->decimal('monto_pagado', 10, 2)->default(0)->after('total');
            }

            if (! Schema::hasColumn('ventas_producto', 'saldo_pendiente')) {
                $table->decimal('saldo_pendiente', 10, 2)->default(0)->after('monto_pagado');
            }

            if (! Schema::hasColumn('ventas_producto', 'estado')) {
                $table->enum('estado', ['pendiente', 'parcial', 'pagado'])->default('pendiente')->after('saldo_pendiente');
            }

            if (! Schema::hasColumn('ventas_producto', 'metodo_pago')) {
                $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta'])->nullable()->after('estado');
            }

            if (! Schema::hasColumn('ventas_producto', 'referencia')) {
                $table->string('referencia', 100)->nullable()->after('metodo_pago');
            }
        });
    }

    /**
     * Documentacion: Revierte los cambios de la migracion.
     * Como lo hace: Elimina o restaura estructuras para regresar al estado anterior.
     */
    public function down(): void
    {
        Schema::table('ventas_producto', function (Blueprint $table) {
            if (Schema::hasColumn('ventas_producto', 'referencia')) {
                $table->dropColumn('referencia');
            }

            if (Schema::hasColumn('ventas_producto', 'metodo_pago')) {
                $table->dropColumn('metodo_pago');
            }

            if (Schema::hasColumn('ventas_producto', 'estado')) {
                $table->dropColumn('estado');
            }

            if (Schema::hasColumn('ventas_producto', 'saldo_pendiente')) {
                $table->dropColumn('saldo_pendiente');
            }

            if (Schema::hasColumn('ventas_producto', 'monto_pagado')) {
                $table->dropColumn('monto_pagado');
            }
        });
    }
};
