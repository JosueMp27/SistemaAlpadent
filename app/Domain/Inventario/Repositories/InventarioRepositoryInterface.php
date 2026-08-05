<?php

namespace App\Domain\Inventario\Repositories;

use App\Models\MovimientoInventario;
use App\Models\VentaProducto;

interface InventarioRepositoryInterface
{
    public function registrarEntrada(array $datos): MovimientoInventario;
    public function registrarSalida(array $datos): MovimientoInventario;
    public function registrarVenta(array $datos): VentaProducto;
    public function registrarAbonoVenta(int $ventaId, array $datos): array;
    public function obtenerStockBajo();
    public function obtenerMovimientos(int $productoId, bool $paginated = true);
    public function obtenerVentas($fechaInicio = null, $fechaFin = null, bool $paginated = true, ?int $pacienteId = null, ?string $estado = null);
    public function obtenerDetalleVenta(int $ventaId): VentaProducto;
    public function obtenerProductos(bool $paginated = true);
    public function buscar(string $termino, bool $paginated = true);
    public function obtenerEstadisticas();
}
