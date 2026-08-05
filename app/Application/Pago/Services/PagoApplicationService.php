<?php

namespace App\Application\Pago\Services;

use App\Domain\Pago\Repositories\PagoRepositoryInterface;
use App\Models\Pago;
use App\Models\Abono;

class PagoApplicationService
{
    public function __construct(
        private PagoRepositoryInterface $pagoRepository
    ) {}

    public function registrar(array $datos): Pago
    {
        return $this->pagoRepository->registrar($datos);
    }

    public function registrarAbono(array $datos): Abono
    {
        return $this->pagoRepository->registrarAbono($datos);
    }

    public function obtenerCitasParaCobro(?int $pacienteId = null, ?string $search = null)
    {
        return $this->pagoRepository->obtenerCitasParaCobro($pacienteId, $search);
    }

    public function obtenerDetalleCitaPago(int $citaId): array
    {
        return $this->pagoRepository->obtenerDetalleCitaPago($citaId);
    }

    public function cobrarCita(int $citaId, array $datos): array
    {
        return $this->pagoRepository->cobrarCita($citaId, $datos);
    }

    public function obtenerPendientes(int $pacienteId)
    {
        return $this->pagoRepository->obtenerPendientes($pacienteId);
    }

    public function obtenerHistorial(int $pacienteId, bool $paginated = true)
    {
        return $this->pagoRepository->obtenerHistorial($pacienteId, $paginated);
    }

    public function obtenerSaldoTotalPaciente(int $pacienteId): float
    {
        return $this->pagoRepository->obtenerSaldoTotalPaciente($pacienteId);
    }

    public function obtenerDetalles(int $pagoId)
    {
        return $this->pagoRepository->obtenerDetalles($pagoId);
    }

    public function obtenerDeudores(bool $paginated = true)
    {
        return $this->pagoRepository->obtenerDeudores($paginated);
    }

    public function obtenerReporteIngresos($fechaInicio, $fechaFin)
    {
        return $this->pagoRepository->obtenerReporteIngresos($fechaInicio, $fechaFin);
    }

    public function calcularIngresosMes($mes, $anio): float
    {
        return $this->pagoRepository->calcularIngresosMes($mes, $anio);
    }

    public function obtenerEstadisticas()
    {
        return $this->pagoRepository->obtenerEstadisticas();
    }

    public function obtenerMetodosMasUsados()
    {
        return $this->pagoRepository->obtenerMetodosMasUsados();
    }
}
