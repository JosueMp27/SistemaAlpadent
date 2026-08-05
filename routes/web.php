<?php

/**
 * Documentacion de archivo:
 * Mapa de rutas web que entrega vistas Blade; cada URL carga una pantalla del sistema administrativo.
 *
 * Mantiene la documentacion dentro del codigo para facilitar el estudio y mantenimiento del proyecto.
 */

use Illuminate\Support\Facades\Route;


// Documentacion: cada ruta web devuelve una vista Blade; la logica de datos se consume desde la API con JavaScript.
Route::get('/dashboard', function () {
    return view('dashboard.index');
});

// Documentacion: la raiz muestra el login, donde se obtiene el token para consumir endpoints protegidos.
Route::get('/', function () {
    return view('auth.login');
});

// Documentacion: pantallas principales de gestion clinica y administrativa.
Route::get('/pacientes', function () {
    return view('pacientes.index');
});

Route::get('/citas', function () {
    return view('citas.index');
});

Route::get('/doctores-externos', function () {
    return view('doctores_externos.index');
});

Route::get('/diagnosticos', function () {
    return view('diagnosticos.index');
});

Route::get('/pagos', function () {
    return view('pagos.index');
});

Route::get('/pagos/citas', function () {
    return view('pagos.index');
});

Route::get('/pagos/productos', function () {
    return view('pagos.productos');
});

// Documentacion: reportes se separa en catalogo y detalle dinamico por tipo.
Route::get('/reportes', function () {
    return view('reportes.index');
});

Route::get('/reportes/{tipo}', function (string $tipo) {
    return view('reportes.show', ['tipo' => $tipo]);
});

Route::get('/configuracion', function () {
    return view('configuracion.index');
});

Route::get('/configuracion/odontograma', function () {
    return view('configuracion.odontograma');
});

Route::get('/configuracion/usuarios', function () {
    return view('configuracion.usuarios');
});

Route::get('/configuracion/tratamientos', function () {
    return view('configuracion.tratamientos');
});

// Documentacion: inventario separa productos disponibles y ventas realizadas.
Route::get('/inventario/productos', function () {
    return view('inventario.productos');
});

Route::get('/inventario/ventas', function () {
    return view('inventario.ventas');
});

Route::get('/odontograma', function () {
    return view('odontogramas.index');
});
