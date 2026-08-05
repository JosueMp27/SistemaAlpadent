<?php

namespace App\Application\Inventario\Services;

use App\Domain\Inventario\Repositories\InventarioRepositoryInterface;
use App\Models\MovimientoInventario;
use App\Models\VentaProducto;

class InventarioApplicationService
{
    public function __construct(
        private InventarioRepositoryInterface $inventarioRepository
    ) {}

    public function registrarEntrada(array $datos): MovimientoInventario
    {
        return $this->inventarioRepository->registrarEntrada($datos);
    }

    public function registrarSalida(array $datos): MovimientoInventario
    {
        return $this->inventarioRepository->registrarSalida($datos);
    }

    public function registrarVenta(array $datos): VentaProducto
    {
        return $this->inventarioRepository->registrarVenta($datos);
    }

    public function registrarAbonoVenta(int $ventaId, array $datos): array
    {
        return $this->inventarioRepository->registrarAbonoVenta($ventaId, $datos);
    }

    public function obtenerStockBajo()
    {
        return $this->inventarioRepository->obtenerStockBajo();
    }

    public function obtenerMovimientos(int $productoId, bool $paginated = true)
    {
        return $this->inventarioRepository->obtenerMovimientos($productoId, $paginated);
    }

    public function obtenerVentas($fechaInicio = null, $fechaFin = null, bool $paginated = true, ?int $pacienteId = null, ?string $estado = null)
    {
        return $this->inventarioRepository->obtenerVentas($fechaInicio, $fechaFin, $paginated, $pacienteId, $estado);
    }

    public function obtenerDetalleVenta(int $ventaId): VentaProducto
    {
        return $this->inventarioRepository->obtenerDetalleVenta($ventaId);
    }

    public function obtenerProductos(bool $paginated = true)
    {
        return $this->inventarioRepository->obtenerProductos($paginated);
    }

    public function buscar(string $termino, bool $paginated = true)
    {
        return $this->inventarioRepository->buscar($termino, $paginated);
    }

    public function obtenerEstadisticas()
    {
        return $this->inventarioRepository->obtenerEstadisticas();
    }
}
