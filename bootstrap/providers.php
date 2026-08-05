<?php

/**
 * Documentacion de archivo:
 * Archivo de arranque de Laravel; conecta rutas, middleware, proveedores y manejo inicial de la aplicacion.
 *
 * Mantiene la documentacion dentro del codigo para facilitar el estudio y mantenimiento del proyecto.
 */

use App\Providers\AppServiceProvider;
use App\Providers\HexagonalServiceProvider;

return [
    AppServiceProvider::class,
    HexagonalServiceProvider::class,
];
