<?php

namespace App\Services;

use App\Models\AbonoVentaProducto;
use App\Models\DetalleVenta;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\VentaProducto;
use Illuminate\Support\Facades\DB;

/**
 * Documentacion de clase:
 * Servicio de negocio que concentra reglas, transacciones y consultas Eloquent para mantener limpios los controladores.
 */
class InventarioService
{
    /**
     * Documentacion: Registra entrada de inventario.
     * Como lo hace: Incrementa stock y crea un movimiento de tipo entrada dentro de una transaccion.
     */
    public function registrarEntrada(array $datos): MovimientoInventario
    {
        $producto = Producto::where('id', $datos['producto_id'])
            ->where('activo', true)
            ->firstOrFail();

        return DB::transaction(function () use ($datos, $producto) {
            $producto->increment('stock_actual', $datos['cantidad']);

            return MovimientoInventario::create([
                'producto_id' => $producto->id,
                'usuario_id' => $datos['usuario_id'],
                'tipo_movimiento' => 'entrada',
                'cantidad' => $datos['cantidad'],
                'descripcion' => $datos['motivo'],
                'created_at' => now(),
            ])->fresh();
        });
    }

    /**
     * Documentacion: Registra salida manual de inventario.
     * Como lo hace: Valida stock suficiente, descuenta unidades y crea el movimiento de salida.
     */
    public function registrarSalida(array $datos): MovimientoInventario
    {
        $producto = Producto::where('id', $datos['producto_id'])
            ->where('activo', true)
            ->firstOrFail();

        if ($producto->stock_actual < $datos['cantidad']) {
            throw new \Exception('Stock insuficiente para realizar la salida.');
        }

        return DB::transaction(function () use ($datos, $producto) {
            $producto->decrement('stock_actual', $datos['cantidad']);

            return MovimientoInventario::create([
                'producto_id' => $producto->id,
                'usuario_id' => $datos['usuario_id'],
                'tipo_movimiento' => 'salida',
                'cantidad' => $datos['cantidad'],
                'descripcion' => $datos['motivo'],
                'created_at' => now(),
            ])->fresh();
        });
    }

    /**
     * Documentacion: Registra una venta de productos.
     * Como lo hace: Bloquea productos, valida stock, calcula total, descuenta inventario y crea detalle, movimiento y abono inicial.
     */
    public function registrarVenta(array $datos): VentaProducto
    {
        return DB::transaction(function () use ($datos) {
            $productosSolicitados = collect($datos['productos']);
            $productos = Producto::whereIn('id', $productosSolicitados->pluck('producto_id'))
                ->where('activo', true)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $totalVenta = 0;

            foreach ($productosSolicitados as $item) {
                $producto = $productos->get((int) $item['producto_id']);

                if (! $producto) {
                    throw new \Exception('Uno de los productos seleccionados no existe o esta inactivo.');
                }

                if ($producto->stock_actual < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para {$producto->nombre}.");
                }

                $totalVenta += round($producto->precio_venta * $item['cantidad'], 2);
            }

            $montoPagado = min(round((float) ($datos['monto_pagado'] ?? 0), 2), $totalVenta);
            $saldoPendiente = max(round($totalVenta - $montoPagado, 2), 0);

            $venta = VentaProducto::create([
                'paciente_id' => $datos['paciente_id'] ?? null,
                'usuario_id' => $datos['usuario_id'],
                'total' => $totalVenta,
                'monto_pagado' => $montoPagado,
                'saldo_pendiente' => $saldoPendiente,
                'estado' => $this->calcularEstadoPago($saldoPendiente, $montoPagado),
                'metodo_pago' => $datos['metodo_pago'] ?? null,
                'referencia' => $datos['referencia'] ?? null,
                'observaciones' => $datos['observaciones'] ?? null,
            ]);

            foreach ($productosSolicitados as $item) {
                $producto = $productos->get((int) $item['producto_id']);
                $subtotal = round($producto->precio_venta * $item['cantidad'], 2);

                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio_venta,
                    'subtotal' => $subtotal,
                ]);

                $producto->decrement('stock_actual', $item['cantidad']);

                MovimientoInventario::create([
                    'producto_id' => $producto->id,
                    'usuario_id' => $datos['usuario_id'],
                    'tipo_movimiento' => 'salida',
                    'cantidad' => $item['cantidad'],
                    'descripcion' => "Venta de productos #{$venta->id}",
                    'created_at' => now(),
                ]);
            }

            if ($montoPagado > 0) {
                AbonoVentaProducto::create([
                    'venta_id' => $venta->id,
                    'usuario_id' => $datos['usuario_id'],
                    'monto' => $montoPagado,
                    'metodo_pago' => $datos['metodo_pago'] ?? 'efectivo',
                    'referencia' => $datos['referencia'] ?? null,
                    'observaciones' => 'Pago inicial de venta',
                    'fecha' => now(),
                ]);
            }

            return $venta->fresh()->load(['detalles.producto', 'usuario', 'paciente', 'abonos.usuario']);
        });
    }

    /**
     * Documentacion: Registra un abono de venta de productos.
     * Como lo hace: Bloquea la venta, aplica solo el saldo pendiente, calcula vuelto y actualiza estado.
     */
    public function registrarAbonoVenta(int $ventaId, array $datos): array
    {
        return DB::transaction(function () use ($ventaId, $datos) {
            $venta = VentaProducto::lockForUpdate()->findOrFail($ventaId);

            if ($venta->estado === 'pagado') {
                throw new \Exception('Esta venta ya fue pagada en su totalidad.');
            }

            $saldoAntes = round((float) $venta->saldo_pendiente, 2);
            $montoRecibido = round((float) $datos['monto'], 2);
            $montoAplicado = min($montoRecibido, $saldoAntes);
            $vuelto = max($montoRecibido - $saldoAntes, 0);

            if ($montoAplicado <= 0) {
                throw new \Exception('No hay saldo pendiente para esta venta.');
            }

            $observaciones = $datos['observaciones'] ?? null;
            if ($vuelto > 0) {
                $observaciones = trim(($observaciones ? $observaciones . ' ' : '') .
                    'Monto recibido: $' . number_format($montoRecibido, 2) .
                    '. Vuelto a dar: $' . number_format($vuelto, 2) . '.');
            }

            $abono = AbonoVentaProducto::create([
                'venta_id' => $venta->id,
                'usuario_id' => $datos['usuario_id'],
                'monto' => $montoAplicado,
                'metodo_pago' => $datos['metodo_pago'],
                'referencia' => $datos['referencia'] ?? null,
                'observaciones' => $observaciones,
                'fecha' => now(),
            ]);

            $nuevoPagado = round((float) $venta->monto_pagado + $montoAplicado, 2);
            $nuevoSaldo = max(round((float) $venta->total - $nuevoPagado, 2), 0);

            $venta->update([
                'monto_pagado' => $nuevoPagado,
                'saldo_pendiente' => $nuevoSaldo,
                'estado' => $this->calcularEstadoPago($nuevoSaldo, $nuevoPagado),
                'metodo_pago' => $datos['metodo_pago'],
                'referencia' => $datos['referencia'] ?? $venta->referencia,
            ]);

            return [
                'venta' => $venta->fresh()->load(['detalles.producto', 'usuario', 'paciente', 'abonos.usuario']),
                'abono' => $abono->fresh()->load('usuario'),
                'monto_aplicado' => $montoAplicado,
                'vuelto' => $vuelto,
            ];
        });
    }

    /**
     * Documentacion: Lista productos con stock bajo.
     * Como lo hace: Filtra productos activos cuyo stock actual esta por debajo o igual al minimo.
     */
    public function obtenerStockBajo()
    {
        return Producto::where('activo', true)
            ->whereRaw('stock_actual <= stock_minimo')
            ->orderBy('stock_actual')
            ->get();
    }

    /**
     * Documentacion: Lista movimientos de inventario de un producto.
     * Como lo hace: Filtra por producto, carga usuario y ordena desde el mas reciente.
     */
    public function obtenerMovimientos(int $productoId, $paginated = true)
    {
        $query = MovimientoInventario::where('producto_id', $productoId)
            ->with('usuario')
            ->orderBy('created_at', 'desc');

        return $paginated ? $query->paginate(20) : $query->get();
    }

    /**
     * Documentacion: Lista ventas de productos.
     * Como lo hace: Carga detalles, paciente y abonos; aplica filtros por fecha, paciente o estado.
     */
    public function obtenerVentas($fechaInicio = null, $fechaFin = null, $paginated = true, ?int $pacienteId = null, ?string $estado = null)
    {
        $query = VentaProducto::with(['detalles.producto', 'usuario', 'paciente', 'abonos.usuario'])
            ->orderBy('created_at', 'desc');

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }

        if ($pacienteId) {
            $query->where('paciente_id', $pacienteId);
        }

        if ($estado) {
            $query->where('estado', $estado);
        }

        return $paginated ? $query->paginate(15) : $query->get();
    }

    /**
     * Documentacion: Obtiene detalle completo de una venta.
     * Como lo hace: Carga productos, usuario, paciente y abonos asociados a la venta.
     */
    public function obtenerDetalleVenta(int $ventaId): VentaProducto
    {
        return VentaProducto::with(['detalles.producto', 'usuario', 'paciente', 'abonos.usuario'])
            ->findOrFail($ventaId);
    }

    /**
     * Documentacion: Lista productos activos.
     * Como lo hace: Ordena por nombre y pagina si la pantalla lo requiere.
     */
    public function obtenerProductos($paginated = true)
    {
        $query = Producto::where('activo', true)
            ->orderBy('nombre', 'asc');

        return $paginated ? $query->paginate(20) : $query->get();
    }

    /**
     * Documentacion: Busca registros por texto.
     * Como lo hace: Aplica condiciones LIKE sobre los campos mas utiles del modulo y pagina la respuesta.
     */
    public function buscar(string $termino, $paginated = true)
    {
        $query = Producto::where('activo', true)
            ->where(function ($q) use ($termino) {
                $q->where('nombre', 'like', "%{$termino}%")
                    ->orWhere('marca', 'like', "%{$termino}%")
                    ->orWhere('descripcion', 'like', "%{$termino}%");
            })
            ->orderBy('nombre', 'asc');

        return $paginated ? $query->paginate(15) : $query->get();
    }

    /**
     * Documentacion: Calcula estadisticas del modulo.
     * Como lo hace: Usa conteos, sumas o vistas SQL para devolver indicadores listos para tarjetas o reportes.
     */
    public function obtenerEstadisticas()
    {
        return [
            'total_productos' => Producto::where('activo', true)->count(),
            'productos_stock_bajo' => Producto::where('activo', true)
                ->whereRaw('stock_actual <= stock_minimo')
                ->count(),
            'productos_sin_stock' => Producto::where('activo', true)
                ->where('stock_actual', 0)
                ->count(),
            'valor_total_inventario' => DB::table('productos')
                ->where('activo', true)
                ->selectRaw('SUM(stock_actual * precio_venta) as total')
                ->value('total') ?? 0,
            'total_ventas_mes' => VentaProducto::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total'),
            'saldo_productos_pendiente' => VentaProducto::whereIn('estado', ['pendiente', 'parcial'])
                ->sum('saldo_pendiente'),
            'promedio_precio_venta' => Producto::where('activo', true)->avg('precio_venta') ?? 0,
        ];
    }

    /**
     * Documentacion: Determina el estado financiero de un pago o venta.
     * Como lo hace: Compara saldo pendiente y monto pagado para devolver pendiente, parcial o pagado.
     */
    private function calcularEstadoPago(float $saldoPendiente, float $montoPagado): string
    {
        if ($saldoPendiente <= 0) {
            return 'pagado';
        }

        return $montoPagado > 0 ? 'parcial' : 'pendiente';
    }
}
