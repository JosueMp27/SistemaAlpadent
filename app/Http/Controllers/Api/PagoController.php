<?php

/**
 * Documentacion de archivo:
 * Controlador API que recibe peticiones HTTP, valida entradas y delega en la capa de aplicacion Hexagonal.
 */

namespace App\Http\Controllers\Api;

use App\Http\Requests\StorePagoRequest;
use App\Http\Requests\StoreAbonoRequest;
use App\Application\Pago\Services\PagoApplicationService;
use App\Models\Pago;
use Illuminate\Http\Request;

class PagoController extends ApiController
{
    public function __construct(
        protected PagoApplicationService $pagoService
    ) {}

    public function index(Request $request)
    {
        try {
            $estado = $request->query('estado');
            $pacienteId = $request->query('paciente_id');

            $query = Pago::with(['paciente', 'cita', 'usuario', 'abonos'])
                ->orderBy('created_at', 'desc');

            if ($estado) {
                $query->where('estado', $estado);
            }

            if ($pacienteId) {
                $query->where('paciente_id', $pacienteId);
            }

            $pagos = $query->paginate(15);
            return $this->successResponse($pagos, 'Pagos obtenidos correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function citas(Request $request)
    {
        try {
            $pacienteId = $request->query('paciente_id');
            $search = $request->query('search');

            $citas = $this->pagoService->obtenerCitasParaCobro(
                $pacienteId ? (int) $pacienteId : null,
                $search
            );

            return $this->successResponse($citas, 'Citas para pagos obtenidas correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function detalleCita($citaId)
    {
        try {
            $detalle = $this->pagoService->obtenerDetalleCitaPago((int) $citaId);
            return $this->successResponse($detalle, 'Detalle de pago obtenido correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function cobrarCita(Request $request, $citaId)
    {
        try {
            $datos = $request->validate([
                'usuario_id' => 'required|exists:usuarios,id',
                'monto' => 'required|numeric|min:0.01',
                'metodo_pago' => 'required|in:efectivo,transferencia,tarjeta',
                'referencia' => 'required_if:metodo_pago,transferencia|nullable|string|max:100',
                'observaciones' => 'nullable|string|max:500',
            ]);

            $resultado = $this->pagoService->cobrarCita((int) $citaId, $datos);

            return $this->successResponse($resultado, 'Cobro registrado correctamente', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationFailedResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function store(StorePagoRequest $request)
    {
        try {
            $pago = $this->pagoService->registrar($request->validated());
            return $this->successResponse($pago, 'Pago registrado correctamente', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $pago = $this->pagoService->obtenerDetalles((int) $id);
            return $this->successResponse($pago, 'Pago obtenido correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function registrarAbono(StoreAbonoRequest $request)
    {
        try {
            $abono = $this->pagoService->registrarAbono($request->validated());
            return $this->successResponse($abono, 'Abono registrado correctamente', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function pendientes($pacienteId)
    {
        try {
            $pagos = $this->pagoService->obtenerPendientes((int) $pacienteId);
            return $this->successResponse($pagos, 'Pagos pendientes obtenidos');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function historial($pacienteId)
    {
        try {
            $pagos = $this->pagoService->obtenerHistorial((int) $pacienteId);
            return $this->successResponse($pagos, 'Historial de pagos obtenido');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function saldoPaciente($pacienteId)
    {
        try {
            $saldo = $this->pagoService->obtenerSaldoTotalPaciente((int) $pacienteId);
            return $this->successResponse(['saldo' => $saldo], 'Saldo obtenido correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function deudores(Request $request)
    {
        try {
            $deudores = $this->pagoService->obtenerDeudores();
            return $this->successResponse($deudores, 'Deudores obtenidos correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function reporteIngresos(Request $request)
    {
        try {
            $fechaInicio = $request->query('fecha_inicio');
            $fechaFin = $request->query('fecha_fin');

            if (!$fechaInicio || !$fechaFin) {
                return $this->errorResponse('Las fechas son requeridas', 400);
            }

            $reporte = $this->pagoService->obtenerReporteIngresos($fechaInicio, $fechaFin);
            return $this->successResponse($reporte, 'Reporte de ingresos obtenido');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function ingresosMes(Request $request)
    {
        try {
            $mes = $request->query('mes', now()->month);
            $anio = $request->query('anio', now()->year);

            $ingresos = $this->pagoService->calcularIngresosMes($mes, $anio);
            return $this->successResponse(['ingresos' => $ingresos], 'Ingresos del mes obtenidos');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function estadisticas()
    {
        try {
            $estadisticas = $this->pagoService->obtenerEstadisticas();
            return $this->successResponse($estadisticas, 'Estadísticas obtenidas correctamente');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function metodos()
    {
        try {
            $metodos = $this->pagoService->obtenerMetodosMasUsados();
            return $this->successResponse($metodos, 'Métodos de pago obtenidos');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
