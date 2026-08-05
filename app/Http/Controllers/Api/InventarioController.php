<?php

/**
 * Documentacion de archivo:
 * Controlador API que recibe peticiones HTTP, valida entradas y delega en la capa de aplicacion Hexagonal.
 */

namespace App\Http\Controllers\Api;

use App\Application\Inventario\Services\InventarioApplicationService;
use App\Models\Producto;
use Illuminate\Http\Request;

class InventarioController extends ApiController
{
    public function __construct(
        protected InventarioApplicationService $inventarioService
    ) {}

    public function index(Request $request)
    {
        try {
            $search = $request->query('search');

            if ($search) {
                $productos = $this->inventarioService->buscar($search);
            } else {
                $productos = $this->inventarioService->obtenerProductos();
            }

            return $this->successResponse($productos, 'Productos obtenidos correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $datos = $request->validate([
                'nombre' => 'required|string|unique:productos|min:2|max:150',
                'marca' => 'nullable|string|max:100',
                'descripcion' => 'nullable|string',
                'precio_venta' => 'required|numeric|min:0',
                'stock_actual' => 'required|integer|min:0',
                'stock_minimo' => 'required|integer|min:0',
            ]);

            $producto = Producto::create($datos);
            return $this->successResponse($producto, 'Producto creado correctamente', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $producto = Producto::findOrFail($id);
            return $this->successResponse($producto, 'Producto obtenido correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $producto = Producto::findOrFail($id);

            $datos = $request->validate([
                'nombre' => 'nullable|string|min:2|max:150|unique:productos,nombre,' . $id,
                'marca' => 'nullable|string|max:100',
                'descripcion' => 'nullable|string',
                'precio_venta' => 'nullable|numeric|min:0',
                'stock_actual' => 'nullable|integer|min:0',
                'stock_minimo' => 'nullable|integer|min:0',
                'activo' => 'nullable|boolean',
            ]);

            $producto->update($datos);
            return $this->successResponse($producto->fresh(), 'Producto actualizado correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $producto = Producto::findOrFail($id);
            $producto->update(['activo' => false]);

            return $this->successResponse(null, 'Producto eliminado correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function entrada(Request $request)
    {
        try {
            $datos = $request->validate([
                'producto_id' => 'required|exists:productos,id',
                'usuario_id' => 'required|exists:usuarios,id',
                'cantidad' => 'required|integer|min:1',
                'motivo' => 'required|string|min:3',
            ]);

            $movimiento = $this->inventarioService->registrarEntrada($datos);
            return $this->successResponse($movimiento, 'Entrada registrada correctamente', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function salida(Request $request)
    {
        try {
            $datos = $request->validate([
                'producto_id' => 'required|exists:productos,id',
                'usuario_id' => 'required|exists:usuarios,id',
                'cantidad' => 'required|integer|min:1',
                'motivo' => 'required|string|min:3',
            ]);

            $movimiento = $this->inventarioService->registrarSalida($datos);
            return $this->successResponse($movimiento, 'Salida registrada correctamente', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function venta(Request $request)
    {
        try {
            $datos = $request->validate([
                'paciente_id' => 'nullable|exists:pacientes,id',
                'usuario_id' => 'required|exists:usuarios,id',
                'monto_pagado' => 'nullable|numeric|min:0',
                'metodo_pago' => 'nullable|in:efectivo,transferencia,tarjeta',
                'referencia' => 'nullable|string|max:100',
                'observaciones' => 'nullable|string|max:500',
                'productos' => 'required|array|min:1',
                'productos.*.producto_id' => 'required|exists:productos,id',
                'productos.*.cantidad' => 'required|integer|min:1',
            ]);

            $venta = $this->inventarioService->registrarVenta($datos);
            return $this->successResponse($venta, 'Venta registrada correctamente', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function stockBajo()
    {
        try {
            $productos = $this->inventarioService->obtenerStockBajo();
            return $this->successResponse($productos, 'Productos con stock bajo obtenidos');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function movimientos($productoId, Request $request)
    {
        try {
            $movimientos = $this->inventarioService->obtenerMovimientos((int) $productoId);
            return $this->successResponse($movimientos, 'Movimientos obtenidos correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function ventas(Request $request)
    {
        try {
            $fechaInicio = $request->query('fecha_inicio');
            $fechaFin = $request->query('fecha_fin');
            $pacienteId = $request->query('paciente_id');
            $estado = $request->query('estado');

            $ventas = $this->inventarioService->obtenerVentas(
                $fechaInicio,
                $fechaFin,
                true,
                $pacienteId ? (int) $pacienteId : null,
                $estado
            );
            return $this->successResponse($ventas, 'Ventas obtenidas correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function showVenta($id)
    {
        try {
            $venta = $this->inventarioService->obtenerDetalleVenta((int) $id);

            return $this->successResponse($venta, 'Venta obtenida correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function cobrarVenta(Request $request, $id)
    {
        try {
            $datos = $request->validate([
                'usuario_id' => 'required|exists:usuarios,id',
                'monto' => 'required|numeric|min:0.01',
                'metodo_pago' => 'required|in:efectivo,transferencia,tarjeta',
                'referencia' => 'required_if:metodo_pago,transferencia|nullable|string|max:100',
                'observaciones' => 'nullable|string|max:500',
            ]);

            $resultado = $this->inventarioService->registrarAbonoVenta((int) $id, $datos);

            return $this->successResponse($resultado, 'Pago de productos registrado correctamente', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationFailedResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function estadisticas()
    {
        try {
            $estadisticas = $this->inventarioService->obtenerEstadisticas();
            return $this->successResponse($estadisticas, 'Estadísticas obtenidas correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
