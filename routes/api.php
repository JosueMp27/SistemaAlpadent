<?php

/**
 * Documentacion de archivo:
 * Mapa de endpoints REST de la API v1; agrupa autenticacion, dashboard, pacientes, citas, pagos, inventario, odontograma y configuracion.
 *
 * Mantiene la documentacion dentro del codigo para facilitar el estudio y mantenimiento del proyecto.
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\PacienteController;
use App\Http\Controllers\Api\CitaController;
use App\Http\Controllers\Api\DiagnosticoController;
use App\Http\Controllers\Api\PagoController;
use App\Http\Controllers\Api\TratamientoController;
use App\Http\Controllers\Api\InventarioController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\OdontogramaController;
use App\Http\Controllers\Api\DoctorExternoController;
use App\Http\Controllers\Api\ReporteController;
use App\Http\Controllers\Api\ConfiguracionController;
use App\Http\Controllers\Api\ConfiguracionOdontogramaController;
use App\Http\Controllers\Api\ConfiguracionUsuarioController;
use App\Http\Controllers\Api\ConfiguracionTratamientoController;

Route::prefix('v1')->group(function () {
    
    // Documentacion: endpoint simple para comprobar que la API v1 responde.
    Route::get('/test', [TestController::class, 'test']);
    
    // Documentacion: rutas publicas, disponibles antes de tener token de Sanctum.
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Documentacion: todo lo que queda dentro requiere token Bearer valido.
    Route::middleware('auth:sanctum')->group(function () {
        
        // Documentacion: operaciones de sesion y perfil del usuario autenticado.
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/cambiar-password', [AuthController::class, 'cambiarPassword']);
        Route::put('/auth/perfil', [AuthController::class, 'actualizarPerfil']);
        Route::get('/auth/detalles', [AuthController::class, 'detalles']);

        // Documentacion: resumen para tarjetas y citas del calendario principal.
        Route::prefix('dashboard')->group(function () {
            Route::get('/resumen', [DashboardController::class, 'resumen']);
            Route::get('/calendario', [DashboardController::class, 'calendario']);
        });

        // Documentacion: catalogo, vista y exportaciones PDF/Excel de reportes.
        Route::prefix('reportes')->group(function () {
            Route::get('/', [ReporteController::class, 'catalogo']);
            Route::get('/{tipo}', [ReporteController::class, 'show']);
            Route::get('/{tipo}/pdf', [ReporteController::class, 'pdf']);
            Route::get('/{tipo}/excel', [ReporteController::class, 'excel']);
        });

        // Documentacion: configuracion administrativa; requiere middleware admin.
        Route::middleware('admin')->prefix('configuracion')->group(function () {
            Route::get('/', [ConfiguracionController::class, 'index']);

            Route::prefix('odontograma-opciones')->group(function () {
                Route::get('/', [ConfiguracionOdontogramaController::class, 'index']);
                Route::post('/', [ConfiguracionOdontogramaController::class, 'store']);
                Route::get('/{id}', [ConfiguracionOdontogramaController::class, 'show']);
                Route::put('/{id}', [ConfiguracionOdontogramaController::class, 'update']);
                Route::delete('/{id}', [ConfiguracionOdontogramaController::class, 'destroy']);
                Route::post('/{id}/reactivar', [ConfiguracionOdontogramaController::class, 'reactivar']);
            });

            Route::prefix('usuarios')->group(function () {
                Route::get('/', [ConfiguracionUsuarioController::class, 'index']);
                Route::post('/', [ConfiguracionUsuarioController::class, 'store']);
                Route::get('/{id}', [ConfiguracionUsuarioController::class, 'show']);
                Route::put('/{id}', [ConfiguracionUsuarioController::class, 'update']);
                Route::delete('/{id}', [ConfiguracionUsuarioController::class, 'destroy']);
                Route::post('/{id}/reactivar', [ConfiguracionUsuarioController::class, 'reactivar']);
            });

            Route::prefix('tratamientos')->group(function () {
                Route::get('/', [ConfiguracionTratamientoController::class, 'index']);
                Route::post('/', [ConfiguracionTratamientoController::class, 'store']);
                Route::get('/{id}', [ConfiguracionTratamientoController::class, 'show']);
                Route::put('/{id}', [ConfiguracionTratamientoController::class, 'update']);
                Route::delete('/{id}', [ConfiguracionTratamientoController::class, 'destroy']);
                Route::post('/{id}/reactivar', [ConfiguracionTratamientoController::class, 'reactivar']);
            });
        });

        // Pacientes
        Route::prefix('pacientes')->group(function () {
            Route::get('/', [PacienteController::class, 'index']);
            Route::post('/', [PacienteController::class, 'store']);
            Route::get('/{id}', [PacienteController::class, 'show']);
            Route::put('/{id}', [PacienteController::class, 'update']);
            Route::delete('/{id}', [PacienteController::class, 'destroy']);
            Route::get('/{id}/historial', [PacienteController::class, 'historial']);
            Route::get('/{id}/saldo', [PacienteController::class, 'saldo']);
            Route::get('/deudores/listado', [PacienteController::class, 'deudores']);
            Route::post('/{id}/reactivar', [PacienteController::class, 'reactivar']);
        });

        // Citas
        Route::prefix('citas')->group(function () {
            Route::get('/', [CitaController::class, 'index']);
            Route::get('/disponibilidad', [CitaController::class, 'disponibilidadDia']);
            Route::post('/', [CitaController::class, 'store']);
            Route::get('/{id}', [CitaController::class, 'show']);
            Route::put('/{id}', [CitaController::class, 'update']);
            Route::post('/{id}/cancelar', [CitaController::class, 'cancelar']);
            Route::post('/{id}/completar', [CitaController::class, 'completar']);
            Route::post('/{id}/iniciar', [CitaController::class, 'iniciar']);
            Route::get('/agenda/hoy', [CitaController::class, 'agendaHoy']);
            Route::get('/listado/proximas', [CitaController::class, 'proximas']);
        });

        // Doctores externos
        Route::prefix('doctores-externos')->group(function () {
            Route::get('/activos', [DoctorExternoController::class, 'activos']);

            Route::middleware('admin')->group(function () {
                Route::get('/', [DoctorExternoController::class, 'index']);
                Route::post('/', [DoctorExternoController::class, 'store']);
                Route::get('/{id}', [DoctorExternoController::class, 'show']);
                Route::put('/{id}', [DoctorExternoController::class, 'update']);
                Route::delete('/{id}', [DoctorExternoController::class, 'destroy']);
                Route::post('/{id}/reactivar', [DoctorExternoController::class, 'reactivar']);
            });
        });

        // Diagnosticos
        Route::prefix('diagnosticos')->group(function () {
            Route::post('/', [DiagnosticoController::class, 'store']);
            Route::get('/cita/{id}', [DiagnosticoController::class, 'show']);
            Route::post('/{id}/diente', [DiagnosticoController::class, 'agregarDiente']);
            Route::put('/diente/{denteDiagnosticoId}', [DiagnosticoController::class, 'actualizarDiente']);
            Route::delete('/diente/{denteDiagnosticoId}', [DiagnosticoController::class, 'eliminarDiente']);
            Route::get('/paciente/{id}/historial', [DiagnosticoController::class, 'historialPaciente']);
            Route::get('/{id}/odontograma', [DiagnosticoController::class, 'odontograma']);
            Route::get('/listado/recientes', [DiagnosticoController::class, 'recientes']);
            Route::get('/reportes/estadisticas', [DiagnosticoController::class, 'estadisticas']);
        });

        // Odontograma
        Route::prefix('odontogramas')->group(function () {
            Route::get('/catalogos', [OdontogramaController::class, 'catalogos']);
            Route::get('/paciente/{pacienteId}', [OdontogramaController::class, 'showPaciente']);
            Route::post('/paciente/{pacienteId}/marcas', [OdontogramaController::class, 'guardarMarca']);
            Route::put('/paciente/{pacienteId}/indicadores', [OdontogramaController::class, 'actualizarIndicadores']);
            Route::delete('/marcas/{marcaId}', [OdontogramaController::class, 'eliminarMarca']);
        });

        // Tratamientos
        Route::prefix('tratamientos')->group(function () {
            Route::post('/', [TratamientoController::class, 'store']);
            Route::get('/{id}', [TratamientoController::class, 'show']);
            Route::put('/{id}', [TratamientoController::class, 'update']);
            Route::delete('/{id}', [TratamientoController::class, 'destroy']);
            Route::get('/cita/{citaId}', [TratamientoController::class, 'porCita']);
            Route::get('/cita/{citaId}/total', [TratamientoController::class, 'totalCita']);
            Route::get('/listado/frecuentes', [TratamientoController::class, 'frecuentes']);
            Route::get('/reportes/rendimiento', [TratamientoController::class, 'rendimiento']);
            Route::get('/listado/tipos', [TratamientoController::class, 'tipos']);
            Route::get('/listado/categorias', [TratamientoController::class, 'categorias']);
            Route::get('/reportes/estadisticas', [TratamientoController::class, 'estadisticas']);
        });

        // Pagos
        Route::prefix('pagos')->group(function () {
            Route::get('/', [PagoController::class, 'index']);
            Route::post('/', [PagoController::class, 'store']);
            Route::get('/citas', [PagoController::class, 'citas']);
            Route::get('/cita/{citaId}', [PagoController::class, 'detalleCita']);
            Route::post('/cita/{citaId}/cobrar', [PagoController::class, 'cobrarCita']);
            Route::get('/{id}', [PagoController::class, 'show']);
            Route::post('/abono/registrar', [PagoController::class, 'registrarAbono']);
            Route::get('/paciente/{pacienteId}/pendientes', [PagoController::class, 'pendientes']);
            Route::get('/paciente/{pacienteId}/historial', [PagoController::class, 'historial']);
            Route::get('/paciente/{pacienteId}/saldo', [PagoController::class, 'saldoPaciente']);
            Route::get('/listado/deudores', [PagoController::class, 'deudores']);
            Route::get('/reportes/ingresos', [PagoController::class, 'reporteIngresos']);
            Route::get('/reportes/mes', [PagoController::class, 'ingresosMes']);
            Route::get('/reportes/estadisticas', [PagoController::class, 'estadisticas']);
            Route::get('/reportes/metodos', [PagoController::class, 'metodos']);
        });

        // Inventario
        Route::prefix('inventario')->group(function () {
            Route::get('/productos', [InventarioController::class, 'index']);
            Route::get('/productos/{id}', [InventarioController::class, 'show']);
            Route::post('/venta', [InventarioController::class, 'venta']);
            Route::get('/ventas/listado', [InventarioController::class, 'ventas']);
            Route::get('/ventas/{id}', [InventarioController::class, 'showVenta']);
            Route::post('/ventas/{id}/cobrar', [InventarioController::class, 'cobrarVenta']);

            Route::middleware('admin')->group(function () {
                Route::post('/productos', [InventarioController::class, 'store']);
                Route::get('/productos/stock/bajo', [InventarioController::class, 'stockBajo']);
                Route::put('/productos/{id}', [InventarioController::class, 'update']);
                Route::delete('/productos/{id}', [InventarioController::class, 'destroy']);
                Route::post('/entrada', [InventarioController::class, 'entrada']);
                Route::post('/salida', [InventarioController::class, 'salida']);
                Route::get('/movimientos/{productoId}', [InventarioController::class, 'movimientos']);
                Route::get('/reportes/estadisticas', [InventarioController::class, 'estadisticas']);
            });
        });
    });
});
