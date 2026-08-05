<?php

namespace App\Domain\Pago\Repositories;

use App\Models\Pago;
use App\Models\Abono;

interface PagoRepositoryInterface
{
    public function registrar(array $datos): Pago;
    public function registrarAbono(array $datos): Abono;
    public function obtenerCitasParaCobro(?int $pacienteId = null, ?string $search = null);
    public function obtenerDetalleCitaPago(int $citaId): array;
    public function cobrarCita(int $citaId, array $datos): array;
    public function obtenerPendientes(int $pacienteId);
    public function obtenerHistorial(int $pacienteId, bool $paginated = true);
    public function obtenerSaldoTotalPaciente(int $pacienteId): float;
    public function obtenerDetalles(int $pagoId);
    public function obtenerDeudores(bool $paginated = true);
    public function obtenerReporteIngresos($fechaInicio, $fechaFin);
    public function calcularIngresosMes($mes, $anio): float;
    public function obtenerEstadisticas();
    public function obtenerMetodosMasUsados();
}
