<?php

/**
 * Documentacion de archivo:
 * Controlador API que recibe peticiones HTTP, valida entradas, llama servicios o modelos y responde JSON para el frontend.
 *
 * Mantiene la documentacion dentro del codigo para facilitar el estudio y mantenimiento del proyecto.
 */

namespace App\Http\Controllers\Api;

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Pago;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Documentacion de clase:
 * Controlador API que recibe peticiones HTTP, valida entradas, llama servicios o modelos y responde JSON para el frontend.
 */
class DashboardController extends ApiController
{
    /**
     * Documentacion: Calcula los indicadores superiores del dashboard.
     * Como lo hace: Cuenta pacientes, citas, ingresos y productos con stock bajo usando consultas agregadas.
     */
    public function resumen()
    {
        $hoy = Carbon::today();

        // Cada valor se calcula con una consulta agregada para responder rapido y no cargar registros completos.
        $data = [
            'pacientes_activos' => Paciente::where('activo', true)->count(),
            'citas_hoy' => Cita::where('estado', 'programada')
                ->whereDate('fecha_hora_inicio', $hoy)
                ->count(),
            'citas_programadas' => Cita::where('estado', 'programada')->count(),
            'ingresos_mes' => (float) Pago::whereYear('created_at', $hoy->year)
                ->whereMonth('created_at', $hoy->month)
                ->sum('monto_pagado'),
            'stock_bajo' => Producto::where('activo', true)
                ->whereColumn('stock_actual', '<=', 'stock_minimo')
                ->count(),
        ];

        return $this->successResponse($data, 'Resumen del dashboard obtenido correctamente');
    }

    /**
     * Documentacion: Entrega las citas del calendario del dashboard.
     * Como lo hace: Normaliza vista y fecha, calcula un rango y transforma las citas en datos simples para JavaScript.
     */
    public function calendario(Request $request)
    {
        $vista = $request->query('vista', 'mes');
        $fecha = $this->obtenerFechaBase($request->query('fecha'));
        [$inicio, $fin] = $this->obtenerRango($vista, $fecha);

        // El frontend solo pinta citas programadas; por eso se excluyen completadas, canceladas o no asistidas.
        $citas = Cita::with(['paciente', 'tipoTratamiento'])
            ->where('estado', 'programada')
            ->whereBetween('fecha_hora_inicio', [$inicio, $fin])
            ->orderBy('fecha_hora_inicio')
            ->get()
            ->map(function (Cita $cita) {
                // Se normaliza a Carbon para que el formato sea igual aunque el cast del modelo cambie.
                $fechaHora = $cita->fecha_hora_inicio instanceof Carbon
                    ? $cita->fecha_hora_inicio
                    : Carbon::parse($cita->fecha_hora_inicio);

                // La respuesta es plana para que el JavaScript no dependa de la estructura completa de Eloquent.
                return [
                    'id' => $cita->id,
                    'fecha' => $fechaHora->format('Y-m-d'),
                    'hora' => $fechaHora->format('H:i'),
                    'fecha_hora_inicio' => $fechaHora->format('Y-m-d H:i:s'),
                    'paciente' => $cita->paciente
                        ? trim($cita->paciente->nombre . ' ' . $cita->paciente->apellido)
                        : 'Paciente no identificado',
                    'tratamiento' => $cita->tipoTratamiento?->nombre ?? 'Sin tratamiento',
                    'precio' => (float) ($cita->tipoTratamiento?->precio ?? 0),
                    'motivo_consulta' => $cita->motivo_consulta,
                ];
            })
            ->values();

        return $this->successResponse([
            'vista' => in_array($vista, ['dia', 'mes', 'anio'], true) ? $vista : 'mes',
            'inicio' => $inicio->format('Y-m-d'),
            'fin' => $fin->format('Y-m-d'),
            'citas' => $citas,
        ], 'Calendario del dashboard obtenido correctamente');
    }

    /**
     * Documentacion: Normaliza la fecha base del calendario.
     * Como lo hace: Intenta parsear la fecha enviada y cae a hoy; limita el calendario al anio minimo configurado.
     */
    private function obtenerFechaBase(?string $fecha): Carbon
    {
        try {
            $fechaBase = $fecha ? Carbon::parse($fecha) : Carbon::today();
        } catch (\Exception $exception) {
            $fechaBase = Carbon::today();
        }

        // Regla de negocio compartida con el frontend: no navegar antes de la agenda inicial del sistema.
        if ($fechaBase->year < 2026) {
            return Carbon::create(2026, 1, 1);
        }

        return $fechaBase;
    }

    /**
     * Documentacion: Calcula el rango temporal de la vista del calendario.
     * Como lo hace: Devuelve inicio y fin del dia, mes o anio segun la vista solicitada.
     */
    private function obtenerRango(string $vista, Carbon $fecha): array
    {
        return match ($vista) {
            'dia' => [$fecha->copy()->startOfDay(), $fecha->copy()->endOfDay()],
            'anio' => [$fecha->copy()->startOfYear(), $fecha->copy()->endOfYear()],
            default => [$fecha->copy()->startOfMonth(), $fecha->copy()->endOfMonth()],
        };
    }
}
