<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Domain Ports / Interfaces
use App\Domain\Shared\Ports\PdfGeneratorInterface;
use App\Domain\Paciente\Repositories\PacienteRepositoryInterface;
use App\Domain\Cita\Repositories\CitaRepositoryInterface;
use App\Domain\Pago\Repositories\PagoRepositoryInterface;
use App\Domain\Inventario\Repositories\InventarioRepositoryInterface;
use App\Domain\Diagnostico\Repositories\DiagnosticoRepositoryInterface;
use App\Domain\Odontograma\Repositories\OdontogramaRepositoryInterface;
use App\Domain\Tratamiento\Repositories\TratamientoRepositoryInterface;
use App\Domain\Usuario\Repositories\UsuarioRepositoryInterface;
use App\Domain\DoctorExterno\Repositories\DoctorExternoRepositoryInterface;

// Infrastructure Adapters
use App\Infrastructure\Services\SimplePdfAdapter;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentPacienteRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentCitaRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentPagoRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentInventarioRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentDiagnosticoRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentOdontogramaRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentTratamientoRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentUsuarioRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentDoctorExternoRepository;

class HexagonalServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(PdfGeneratorInterface::class, SimplePdfAdapter::class);
        $this->app->bind(PacienteRepositoryInterface::class, EloquentPacienteRepository::class);
        $this->app->bind(CitaRepositoryInterface::class, EloquentCitaRepository::class);
        $this->app->bind(PagoRepositoryInterface::class, EloquentPagoRepository::class);
        $this->app->bind(InventarioRepositoryInterface::class, EloquentInventarioRepository::class);
        $this->app->bind(DiagnosticoRepositoryInterface::class, EloquentDiagnosticoRepository::class);
        $this->app->bind(OdontogramaRepositoryInterface::class, EloquentOdontogramaRepository::class);
        $this->app->bind(TratamientoRepositoryInterface::class, EloquentTratamientoRepository::class);
        $this->app->bind(UsuarioRepositoryInterface::class, EloquentUsuarioRepository::class);
        $this->app->bind(DoctorExternoRepositoryInterface::class, EloquentDoctorExternoRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
