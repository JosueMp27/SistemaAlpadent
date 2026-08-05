<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Pago\Repositories\PagoRepositoryInterface;
use App\Models\Pago;
use App\Models\Abono;
use App\Models\Cita;
use Illuminate\Support\Facades\DB;

class EloquentPagoRepository implements PagoRepositoryInterface
{
    public function registrar(array $datos): Pago
    {
        if (Pago::where('cita_id', $datos['cita_id'])->exists()) {
            throw new \Exception('Esta cita ya tiene un pago registrado.');
        }

        return DB::transaction(function () use ($datos) {
            $montoTotal = (float) $datos['monto_total'];
            $montoPagado = (float) ($datos['monto_pagado'] ?? 0);
            $saldoPendiente = max($montoTotal - $montoPagado, 0);

            $pago = Pago::create([
                'paciente_id' => $datos['paciente_id'],
                'cita_id' => $datos['cita_id'],
                'usuario_id' => $datos['usuario_id'],
                'monto_total' => $montoTotal,
                'monto_pagado' => $montoPagado,
                'saldo_pendiente' => $saldoPendiente,
                'estado' => $this->calcularEstadoPago($saldoPendiente, $montoPagado),
                'metodo_pago' => $datos['metodo_pago'],
                'referencia_transferencia' => $datos['referencia_transferencia'] ?? null,
            ]);

            if ($montoPagado > 0) {
                Abono::create([
                    'pago_id' => $pago->id,
                    'usuario_id' => $datos['usuario_id'],
                    'monto' => $montoPagado,
                    'metodo_pago' => $datos['metodo_pago'],
                    'referencia' => $datos['referencia_transferencia'] ?? null,
                    'fecha' => now(),
                    'observaciones' => 'Pago inicial',
                ]);
            }

            return $pago->fresh()->load(['paciente', 'cita.tipoTratamiento', 'abonos.usuario', 'usuario']);
        });
    }

    public function registrarAbono(array $datos): Abono
    {
        $pago = Pago::findOrFail($datos['pago_id']);

        if ($pago->estaPagado()) {
            throw new \Exception('Este pago ya fue cancelado en su totalidad.');
        }

        return DB::transaction(function () use ($datos, $pago) {
            $montoRecibido = round((float) $datos['monto'], 2);
            $saldoPendiente = round((float) $pago->saldo_pendiente, 2);
            $montoAplicado = min($montoRecibido, $saldoPendiente);
            $vuelto = max($montoRecibido - $saldoPendiente, 0);

            if ($montoAplicado <= 0) {
                throw new \Exception('No hay saldo pendiente para este pago.');
            }

            $observaciones = $datos['observaciones'] ?? null;

            if ($vuelto > 0) {
                $observaciones = trim(($observaciones ? $observaciones . ' ' : '') .
                    'Monto recibido: $' . number_format($montoRecibido, 2) .
                    '. Vuelto a dar: $' . number_format($vuelto, 2) . '.');
            }

            $abono = Abono::create([
                'pago_id' => $datos['pago_id'],
                'usuario_id' => $datos['usuario_id'],
                'monto' => $montoAplicado,
                'metodo_pago' => $datos['metodo_pago'],
                'referencia' => $datos['referencia'] ?? null,
                'fecha' => now(),
                'observaciones' => $observaciones,
            ]);

            $nuevoPagado = round((float) $pago->monto_pagado + $montoAplicado, 2);
            $nuevoSaldo = max(round((float) $pago->monto_total - $nuevoPagado, 2), 0);

            $pago->update([
                'monto_pagado' => $nuevoPagado,
                'saldo_pendiente' => $nuevoSaldo,
                'estado' => $this->calcularEstadoPago($nuevoSaldo, $nuevoPagado),
                'metodo_pago' => $datos['metodo_pago'],
                'referencia_transferencia' => $datos['referencia'] ?? $pago->referencia_transferencia,
            ]);

            return $abono->fresh();
        });
    }

    public function obtenerCitasParaCobro(?int $pacienteId = null, ?string $search = null)
    {
        $query = Cita::with([
                'paciente',
                'usuario',
                'doctorExterno',
                'tipoTratamiento',
                'pago.abonos',
            ])
            ->whereNotIn('estado', ['cancelada', 'no_asistio'])
            ->orderBy('fecha_hora_inicio', 'desc');

        if ($pacienteId) {
            $query->where('paciente_id', $pacienteId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('motivo_consulta', 'like', "%{$search}%")
                    ->orWhereHas('paciente', function ($pacienteQuery) use ($search) {
                        $pacienteQuery->where('nombre', 'like', "%{$search}%")
                            ->orWhere('apellido', 'like', "%{$search}%")
                            ->orWhere('numero_historia', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('tipoTratamiento', function ($tratamientoQuery) use ($search) {
                        $tratamientoQuery->where('nombre', 'like', "%{$search}%");
                    });
            });
        }

        $citas = $query->paginate(15);

        $citas->getCollection()->transform(function (Cita $cita) {
            return $this->formatearCitaParaPago($cita);
        });

        return $citas;
    }

    public function obtenerDetalleCitaPago(int $citaId): array
    {
        $cita = Cita::with([
                'paciente',
                'usuario',
                'doctorExterno',
                'tipoTratamiento',
                'pago.abonos.usuario',
                'pago.usuario',
            ])
            ->findOrFail($citaId);

        $pago = $cita->pago;

        return [
            'cita' => $this->formatearCitaParaPago($cita),
            'paciente' => $cita->paciente,
            'medico' => $this->obtenerNombreMedico($cita),
            'pago' => $pago,
            'movimientos' => $pago
                ? $pago->abonos->sortBy('fecha')->values()->map(function (Abono $abono) {
                    return [
                        'id' => $abono->id,
                        'fecha' => optional($abono->fecha)->format('Y-m-d'),
                        'hora' => optional($abono->fecha)->format('H:i:s'),
                        'monto' => (float) $abono->monto,
                        'metodo_pago' => $abono->metodo_pago,
                        'referencia' => $abono->referencia,
                        'observaciones' => $abono->observaciones,
                        'recibio' => $abono->usuario
                            ? trim($abono->usuario->nombre . ' ' . $abono->usuario->apellido)
                            : 'Usuario no identificado',
                    ];
                })->values()
                : collect(),
        ];
    }

    public function cobrarCita(int $citaId, array $datos): array
    {
        return DB::transaction(function () use ($citaId, $datos) {
            $cita = Cita::with(['paciente', 'tipoTratamiento', 'pago'])->findOrFail($citaId);

            if (! $cita->tipoTratamiento) {
                throw new \Exception('La cita no tiene un tratamiento con precio asignado.');
            }

            $pago = $cita->pago;
            $montoTotal = $pago ? (float) $pago->monto_total : (float) $cita->tipoTratamiento->precio;

            if (! $pago) {
                $pago = Pago::create([
                    'paciente_id' => $cita->paciente_id,
                    'cita_id' => $cita->id,
                    'usuario_id' => $datos['usuario_id'],
                    'monto_total' => $montoTotal,
                    'monto_pagado' => 0,
                    'saldo_pendiente' => $montoTotal,
                    'estado' => 'pendiente',
                    'metodo_pago' => $datos['metodo_pago'],
                    'referencia_transferencia' => $datos['referencia'] ?? null,
                ]);
            }

            $saldoAntes = round((float) $pago->saldo_pendiente, 2);
            $montoRecibido = round((float) $datos['monto'], 2);
            $montoAplicado = min($montoRecibido, $saldoAntes);
            $vuelto = max($montoRecibido - $saldoAntes, 0);

            $abono = $this->registrarAbono([
                'pago_id' => $pago->id,
                'usuario_id' => $datos['usuario_id'],
                'monto' => $montoRecibido,
                'metodo_pago' => $datos['metodo_pago'],
                'referencia' => $datos['referencia'] ?? null,
                'observaciones' => $datos['observaciones'] ?? null,
            ]);

            return [
                'pago' => $pago->fresh()->load(['paciente', 'cita.tipoTratamiento', 'abonos.usuario', 'usuario']),
                'abono' => $abono->fresh()->load('usuario'),
                'monto_aplicado' => $montoAplicado,
                'vuelto' => $vuelto,
            ];
        });
    }

    public function obtenerPendientes(int $pacienteId)
    {
        return Pago::where('paciente_id', $pacienteId)
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->with(['cita.paciente', 'cita.tipoTratamiento', 'abonos'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function obtenerHistorial(int $pacienteId, bool $paginated = true)
    {
        $query = Pago::where('paciente_id', $pacienteId)
            ->with(['cita.tipoTratamiento', 'abonos.usuario', 'usuario'])
            ->orderBy('created_at', 'desc');

        if ($paginated) {
            return $query->paginate(15);
        }

        return $query->get();
    }

    public function obtenerSaldoTotalPaciente(int $pacienteId): float
    {
        return Pago::where('paciente_id', $pacienteId)
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->sum('saldo_pendiente');
    }

    public function obtenerDetalles(int $pagoId)
    {
        return Pago::with(['paciente', 'cita.tipoTratamiento', 'abonos.usuario', 'usuario'])
            ->findOrFail($pagoId);
    }

    public function obtenerDeudores(bool $paginated = true)
    {
        $query = DB::table('vw_pacientes_deudores');

        if ($paginated) {
            return $query->paginate(15);
        }

        return $query->get();
    }

    public function obtenerReporteIngresos($fechaInicio, $fechaFin)
    {
        return DB::select(
            'CALL sp_reporte_ingresos(?, ?)',
            [$fechaInicio, $fechaFin]
        );
    }

    public function calcularIngresosMes($mes, $anio): float
    {
        $resultado = DB::selectOne(
            'SELECT fn_total_ingresos_mes(?, ?) as total',
            [$mes, $anio]
        );

        return $resultado->total ?? 0;
    }

    public function obtenerEstadisticas()
    {
        return [
            'total_pagos' => Pago::count(),
            'total_ingresado' => Pago::sum('monto_pagado'),
            'total_pendiente' => Pago::sum('saldo_pendiente'),
            'pagos_completados' => Pago::where('estado', 'pagado')->count(),
            'pagos_pendientes' => Pago::where('estado', 'pendiente')->count(),
            'pagos_parciales' => Pago::where('estado', 'parcial')->count(),
            'ingreso_promedio' => Pago::avg('monto_pagado') ?? 0,
        ];
    }

    public function obtenerMetodosMasUsados()
    {
        return DB::table('abonos')
            ->selectRaw('metodo_pago, COUNT(*) as cantidad, SUM(monto) as total')
            ->groupBy('metodo_pago')
            ->orderByDesc('cantidad')
            ->get();
    }

    private function formatearCitaParaPago(Cita $cita): array
    {
        $pago = $cita->pago;
        $precioTratamiento = (float) ($cita->tipoTratamiento?->precio ?? 0);
        $costo = (float) ($pago?->monto_total ?? $precioTratamiento);
        $pagadoPorAbonos = $pago ? (float) $pago->abonos->sum('monto') : 0.0;
        $pagado = $pago ? max((float) $pago->monto_pagado, $pagadoPorAbonos) : 0.0;
        $saldo = max($costo - $pagado, 0);
        $estadoPago = $saldo <= 0 && $costo > 0 ? 'pagado' : 'pendiente';

        return [
            'cita_id' => $cita->id,
            'pago_id' => $pago?->id,
            'numero_historia' => $cita->paciente?->numero_historia ?? 'N/A',
            'paciente_id' => $cita->paciente_id,
            'paciente' => $cita->paciente
                ? trim($cita->paciente->nombre . ' ' . $cita->paciente->apellido)
                : 'Paciente no identificado',
            'fecha_hora_inicio' => optional($cita->fecha_hora_inicio)->format('Y-m-d H:i:s'),
            'fecha' => optional($cita->fecha_hora_inicio)->format('Y-m-d'),
            'hora' => optional($cita->fecha_hora_inicio)->format('H:i'),
            'tratamiento' => $cita->tipoTratamiento?->nombre ?? 'Sin tratamiento',
            'precio' => $precioTratamiento,
            'motivo' => $cita->motivo_consulta,
            'estado_cita' => $cita->estado,
            'estado_pago' => $estadoPago,
            'costo' => $costo,
            'pagado' => $pagado,
            'saldo' => $saldo,
        ];
    }

    private function obtenerNombreMedico(Cita $cita): string
    {
        if ($cita->doctorExterno) {
            return trim($cita->doctorExterno->nombre . ' ' . $cita->doctorExterno->apellido);
        }

        if ($cita->usuario) {
            return trim($cita->usuario->nombre . ' ' . $cita->usuario->apellido);
        }

        return 'No asignado';
    }

    private function calcularEstadoPago(float $saldoPendiente, float $montoPagado): string
    {
        if ($saldoPendiente <= 0) {
            return 'pagado';
        }

        if ($montoPagado > 0) {
            return 'parcial';
        }

        return 'pendiente';
    }
}
