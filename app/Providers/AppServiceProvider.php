<?php

/**
 * Documentacion de archivo:
 * Proveedor de Laravel que registra servicios compartidos y configuraciones globales de la aplicacion.
 *
 * Mantiene la documentacion dentro del codigo para facilitar el estudio y mantenimiento del proyecto.
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PacienteService;
use App\Services\CitaService;
use App\Services\DiagnosticoService;
use App\Services\TratamientoService;
use App\Services\PagoService;
use App\Services\InventarioService;
use App\Services\UsuarioService;
use App\Services\OdontogramaService;

/**
 * Documentacion de clase:
 * Proveedor de Laravel que registra servicios compartidos y configuraciones globales de la aplicacion.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    /**
     * Documentacion: Registra servicios en el contenedor de Laravel.
     * Como lo hace: Declara singletons para reutilizar las clases de servicio por inyeccion de dependencias.
     */
    public function register(): void
    {
        // Registrar servicios para inyección de dependencias
        $this->app->singleton(PacienteService::class, function ($app) {
            return new PacienteService();
        });

        $this->app->singleton(CitaService::class, function ($app) {
            return new CitaService();
        });

        $this->app->singleton(DiagnosticoService::class, function ($app) {
            return new DiagnosticoService();
        });

        $this->app->singleton(TratamientoService::class, function ($app) {
            return new TratamientoService();
        });

        $this->app->singleton(PagoService::class, function ($app) {
            return new PagoService();
        });

        $this->app->singleton(InventarioService::class, function ($app) {
            return new InventarioService();
        });

        $this->app->singleton(UsuarioService::class, function ($app) {
            return new UsuarioService();
        });

        $this->app->singleton(OdontogramaService::class, function ($app) {
            return new OdontogramaService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    /**
     * Documentacion: Ejecuta configuracion al arrancar la aplicacion.
     * Como lo hace: Queda disponible para inicializaciones globales despues del registro de servicios.
     */
    public function boot(): void
    {
        //
    }
}
