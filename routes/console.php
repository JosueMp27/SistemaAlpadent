<?php

/**
 * Documentacion de archivo:
 * Archivo de comandos Artisan personalizados disponibles desde la consola de Laravel.
 *
 * Mantiene la documentacion dentro del codigo para facilitar el estudio y mantenimiento del proyecto.
 */

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
