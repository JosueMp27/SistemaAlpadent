<?php

namespace App\Services;

use App\Models\Abono;
use App\Models\AbonoVentaProducto;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Pago;
use App\Models\User;
use App\Models\VentaProducto;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Documentacion de clase:
 * Servicio de negocio que concentra reglas, transacciones y consultas Eloquent para mantener limpios los controladores.
 */
class ReportesService
{
    /**
     * Documentacion: Devuelve el catalogo de reportes disponibles.
     * Como lo hace: Entrega metadatos que el frontend usa para pintar tarjetas y enlaces.
     */
    public function catalogo(): array
    {
        return [
            'pacientes' => [
                'tipo' => 'pacientes',
                'titulo' => 'Reporte de pacientes',
                'detalle' => 'Visualice todos los pacientes registrados, sus datos de contacto, estado e historia clinica.',
                'icono' => 'bi-people',
                'tono' => 'patients',
            ],
            'citas' => [
                'tipo' => 'citas',
                'titulo' => 'Reporte de citas',
                'detalle' => 'Consulte la agenda completa con paciente, tratamiento, profesional asignado y estado de atencion.',
                'icono' => 'bi-calendar2-check',
                'tono' => 'appointments',
            ],
            'pagos' => [
                'tipo' => 'pagos',
                'titulo' => 'Reporte de pagos',
                'detalle' => 'Revise pagos de citas o ventas, montos cancelados, saldos pendientes y abonos registrados.',
                'icono' => 'bi-cash-coin',
                'tono' => 'payments',
            ],
            'movimientos-pagos' => [
                'tipo' => 'movimientos-pagos',
                'titulo' => 'Movimientos de pagos',
                'detalle' => 'Visualice cada abono de citas o ventas, con fecha, monto, metodo, referencia y usuario receptor.',
                'icono' => 'bi-receipt-cutoff',
                'tono' => 'movements',
            ],
        ];
    }

    /**
     * Documentacion: Genera un reporte solicitado.
     * Como lo hace: Valida el tipo, calcula filas y resumen, y agrega metadatos de usuario y fecha.
     */
    public function obtener(string $tipo, ?User $usuario = null, array $opciones = []): array
    {
        $catalogo = $this->catalogo();

        if (! isset($catalogo[$tipo])) {
            throw new InvalidArgumentException('El tipo de reporte no es valido.');
        }

        $origen = $this->normalizarOrigen($tipo, $opciones['origen'] ?? null);

        $datos = match ($tipo) {
            'pacientes' => $this->reportePacientes(),
            'citas' => $this->reporteCitas(),
            'pagos' => $this->reportePagos($origen),
            'movimientos-pagos' => $this->reporteMovimientosPagos($origen),
        };

        $generadoEn = now(config('app.timezone', 'America/Guayaquil'));

        $titulo = $catalogo[$tipo]['titulo'];

        if (in_array($tipo, ['pagos', 'movimientos-pagos'], true)) {
            $titulo .= $origen === 'ventas' ? ' - ventas' : ' - citas';
        }

        return array_merge($catalogo[$tipo], [
            'titulo' => $titulo,
            'generado_por' => $usuario ? trim($usuario->nombre . ' ' . $usuario->apellido) : 'Usuario no identificado',
            'generado_en' => $generadoEn->format('d/m/Y H:i:s'),
            'generado_en_iso' => $generadoEn->format('Y-m-d H:i:s'),
            'origen' => $origen,
            'columnas' => $datos['columnas'],
            'filas' => $datos['filas'],
            'resumen' => $datos['resumen'],
            'total_registros' => count($datos['filas']),
        ]);
    }

    /**
     * Documentacion: Construye el reporte de pacientes.
     * Como lo hace: Carga pacientes con conteos, arma filas legibles y calcula resumen de estados.
     */
    private function reportePacientes(): array
    {
        $pacientes = Paciente::withCount(['citas', 'pagos'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filas = $pacientes->map(function (Paciente $paciente) {
            return [
                'Historia' => $paciente->numero_historia ?? 'N/A',
                'Paciente' => trim($paciente->nombre . ' ' . $paciente->apellido),
                'Edad' => $this->edad($paciente->fecha_nacimiento),
                'Sexo' => $paciente->sexo === 'M' ? 'Masculino' : 'Femenino',
                'Telefono' => $paciente->telefono ?? 'No registrado',
                'Correo' => $paciente->email ?? 'No registrado',
                'Direccion' => $paciente->direccion ?? 'No registrada',
                'Citas' => (string) $paciente->citas_count,
                'Estado' => $paciente->activo ? 'Activo' : 'Inactivo',
                'Registrado' => $this->fecha($paciente->created_at),
            ];
        })->values()->all();

        return [
            'columnas' => ['Historia', 'Paciente', 'Edad', 'Sexo', 'Telefono', 'Correo', 'Direccion', 'Citas', 'Estado', 'Registrado'],
            'filas' => $filas,
            'resumen' => [
                ['label' => 'Pacientes registrados', 'value' => (string) $pacientes->count(), 'tone' => 'primary'],
                ['label' => 'Activos', 'value' => (string) $pacientes->where('activo', true)->count(), 'tone' => 'success'],
                ['label' => 'Inactivos', 'value' => (string) $pacientes->where('activo', false)->count(), 'tone' => 'danger'],
                ['label' => 'Menores de edad', 'value' => (string) $pacientes->where('es_menor', true)->count(), 'tone' => 'warning'],
            ],
        ];
    }

    /**
     * Documentacion: Construye el reporte de citas.
     * Como lo hace: Carga relaciones de agenda y transforma cada cita en columnas imprimibles.
     */
    private function reporteCitas(): array
    {
        $citas = Cita::with(['paciente', 'usuario', 'doctorExterno', 'tipoTratamiento'])
            ->orderBy('fecha_hora_inicio', 'desc')
            ->get();

        $filas = $citas->map(function (Cita $cita) {
            return [
                'Fecha' => $this->fecha($cita->fecha_hora_inicio),
                'Hora' => $this->hora($cita->fecha_hora_inicio),
                'Paciente' => $this->paciente($cita->paciente),
                'Tratamiento' => $cita->tipoTratamiento?->nombre ?? 'Sin tratamiento',
                'Profesional' => $this->profesional($cita),
                'Motivo' => $cita->motivo_consulta ?? 'N/A',
                'Estado' => $this->estado($cita->estado),
                'Primera vez' => $cita->es_primera_vez ? 'Si' : 'No',
                'Registrado por' => $this->usuario($cita->usuario),
            ];
        })->values()->all();

        return [
            'columnas' => ['Fecha', 'Hora', 'Paciente', 'Tratamiento', 'Profesional', 'Motivo', 'Estado', 'Primera vez', 'Registrado por'],
            'filas' => $filas,
            'resumen' => [
                ['label' => 'Citas registradas', 'value' => (string) $citas->count(), 'tone' => 'primary'],
                ['label' => 'Programadas', 'value' => (string) $citas->where('estado', 'programada')->count(), 'tone' => 'info'],
                ['label' => 'Completadas', 'value' => (string) $citas->where('estado', 'completada')->count(), 'tone' => 'success'],
                ['label' => 'Canceladas / no asistio', 'value' => (string) $citas->whereIn('estado', ['cancelada', 'no_asistio'])->count(), 'tone' => 'danger'],
            ],
        ];
    }

    /**
     * Documentacion: Selecciona el reporte de pagos correcto.
     * Como lo hace: Elige entre pagos de citas o ventas segun el origen solicitado.
     */
    private function reportePagos(string $origen): array
    {
        if ($origen === 'ventas') {
            return $this->reportePagosVentas();
        }

        return $this->reportePagosCitas();
    }

    /**
     * Documentacion: Construye el reporte de pagos de citas.
     * Como lo hace: Carga pagos con abonos y calcula totales facturados, pagados y pendientes.
     */
    private function reportePagosCitas(): array
    {
        $pagos = Pago::with(['paciente', 'cita.tipoTratamiento', 'usuario', 'abonos'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filas = $pagos->map(function (Pago $pago) {
            $totalAbonos = (float) $pago->abonos->sum('monto');

            return [
                'Fecha' => $this->fechaHora($pago->created_at),
                'Paciente' => $this->paciente($pago->paciente),
                'Cita' => $pago->cita ? $this->fechaHora($pago->cita->fecha_hora_inicio) : 'N/A',
                'Tratamiento' => $pago->cita?->tipoTratamiento?->nombre ?? 'Sin tratamiento',
                'Total' => $this->dinero($pago->monto_total),
                'Pagado' => $this->dinero($pago->monto_pagado),
                'Saldo' => $this->dinero($pago->saldo_pendiente),
                'Abonos' => $pago->abonos->count() . ' / ' . $this->dinero($totalAbonos),
                'Estado' => $this->estado($pago->estado),
                'Metodo' => $this->estado($pago->metodo_pago),
                'Referencia' => $pago->referencia_transferencia ?? 'N/A',
                'Registrado por' => $this->usuario($pago->usuario),
            ];
        })->values()->all();

        return [
            'columnas' => ['Fecha', 'Paciente', 'Cita', 'Tratamiento', 'Total', 'Pagado', 'Saldo', 'Abonos', 'Estado', 'Metodo', 'Referencia', 'Registrado por'],
            'filas' => $filas,
            'resumen' => [
                ['label' => 'Pagos registrados', 'value' => (string) $pagos->count(), 'tone' => 'primary'],
                ['label' => 'Total facturado', 'value' => $this->dinero($pagos->sum('monto_total')), 'tone' => 'info'],
                ['label' => 'Total pagado', 'value' => $this->dinero($pagos->sum('monto_pagado')), 'tone' => 'success'],
                ['label' => 'Saldo pendiente', 'value' => $this->dinero($pagos->sum('saldo_pendiente')), 'tone' => 'danger'],
            ],
        ];
    }

    /**
     * Documentacion: Construye el reporte de pagos de ventas.
     * Como lo hace: Carga ventas, productos y abonos para mostrar totales y saldos.
     */
    private function reportePagosVentas(): array
    {
        $ventas = VentaProducto::with(['paciente', 'usuario', 'detalles.producto', 'abonos'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filas = $ventas->map(function (VentaProducto $venta) {
            $totalAbonos = (float) $venta->abonos->sum('monto');

            return [
                'Fecha' => $this->fechaHora($venta->created_at),
                'Paciente' => $this->pacienteVenta($venta->paciente),
                'Venta' => '#' . $venta->id,
                'Productos' => $this->productosVenta($venta),
                'Total' => $this->dinero($venta->total),
                'Pagado' => $this->dinero($venta->monto_pagado),
                'Saldo' => $this->dinero($venta->saldo_pendiente),
                'Abonos' => $venta->abonos->count() . ' / ' . $this->dinero($totalAbonos),
                'Estado' => $this->estado($venta->estado),
                'Metodo' => $this->estado($venta->metodo_pago),
                'Referencia' => $venta->referencia ?? 'N/A',
                'Registrado por' => $this->usuario($venta->usuario),
            ];
        })->values()->all();

        return [
            'columnas' => ['Fecha', 'Paciente', 'Venta', 'Productos', 'Total', 'Pagado', 'Saldo', 'Abonos', 'Estado', 'Metodo', 'Referencia', 'Registrado por'],
            'filas' => $filas,
            'resumen' => [
                ['label' => 'Ventas registradas', 'value' => (string) $ventas->count(), 'tone' => 'primary'],
                ['label' => 'Total vendido', 'value' => $this->dinero($ventas->sum('total')), 'tone' => 'info'],
                ['label' => 'Total pagado', 'value' => $this->dinero($ventas->sum('monto_pagado')), 'tone' => 'success'],
                ['label' => 'Saldo pendiente', 'value' => $this->dinero($ventas->sum('saldo_pendiente')), 'tone' => 'danger'],
            ],
        ];
    }

    /**
     * Documentacion: Selecciona el reporte de movimientos correcto.
     * Como lo hace: Elige abonos de citas o ventas segun el origen.
     */
    private function reporteMovimientosPagos(string $origen): array
    {
        if ($origen === 'ventas') {
            return $this->reporteMovimientosVentas();
        }

        return $this->reporteMovimientosCitas();
    }

    /**
     * Documentacion: Construye movimientos de pagos de citas.
     * Como lo hace: Lista abonos con paciente, pago, metodo, referencia y usuario receptor.
     */
    private function reporteMovimientosCitas(): array
    {
        $abonos = Abono::with(['pago.paciente', 'pago.cita.tipoTratamiento', 'usuario'])
            ->orderBy('fecha', 'desc')
            ->get();

        $filas = $abonos->map(function (Abono $abono) {
            return [
                'Fecha' => $this->fecha($abono->fecha),
                'Hora' => $this->hora($abono->fecha),
                'Paciente' => $this->paciente($abono->pago?->paciente),
                'Pago' => $abono->pago ? '#' . $abono->pago->id : 'N/A',
                'Tratamiento' => $abono->pago?->cita?->tipoTratamiento?->nombre ?? 'Sin tratamiento',
                'Monto' => $this->dinero($abono->monto),
                'Metodo' => $this->estado($abono->metodo_pago),
                'Referencia' => $abono->referencia ?? 'N/A',
                'Recibio' => $this->usuario($abono->usuario),
                'Observaciones' => $abono->observaciones ?? 'Sin observaciones',
            ];
        })->values()->all();

        return [
            'columnas' => ['Fecha', 'Hora', 'Paciente', 'Pago', 'Tratamiento', 'Monto', 'Metodo', 'Referencia', 'Recibio', 'Observaciones'],
            'filas' => $filas,
            'resumen' => [
                ['label' => 'Movimientos registrados', 'value' => (string) $abonos->count(), 'tone' => 'primary'],
                ['label' => 'Total recibido', 'value' => $this->dinero($abonos->sum('monto')), 'tone' => 'success'],
                ['label' => 'Efectivo', 'value' => $this->dinero($this->sumarPorMetodo($abonos, 'efectivo')), 'tone' => 'info'],
                ['label' => 'Transferencia / tarjeta', 'value' => $this->dinero($this->sumarPorMetodo($abonos, 'transferencia') + $this->sumarPorMetodo($abonos, 'tarjeta')), 'tone' => 'warning'],
            ],
        ];
    }

    /**
     * Documentacion: Construye movimientos de pagos de ventas.
     * Como lo hace: Lista abonos de ventas con productos, metodo, referencia y usuario receptor.
     */
    private function reporteMovimientosVentas(): array
    {
        $abonos = AbonoVentaProducto::with(['venta.paciente', 'venta.detalles.producto', 'usuario'])
            ->orderBy('fecha', 'desc')
            ->get();

        $filas = $abonos->map(function (AbonoVentaProducto $abono) {
            return [
                'Fecha' => $this->fecha($abono->fecha),
                'Hora' => $this->hora($abono->fecha),
                'Paciente' => $this->pacienteVenta($abono->venta?->paciente),
                'Venta' => $abono->venta ? '#' . $abono->venta->id : 'N/A',
                'Productos' => $abono->venta ? $this->productosVenta($abono->venta) : 'N/A',
                'Monto' => $this->dinero($abono->monto),
                'Metodo' => $this->estado($abono->metodo_pago),
                'Referencia' => $abono->referencia ?? 'N/A',
                'Recibio' => $this->usuario($abono->usuario),
                'Observaciones' => $abono->observaciones ?? 'Sin observaciones',
            ];
        })->values()->all();

        return [
            'columnas' => ['Fecha', 'Hora', 'Paciente', 'Venta', 'Productos', 'Monto', 'Metodo', 'Referencia', 'Recibio', 'Observaciones'],
            'filas' => $filas,
            'resumen' => [
                ['label' => 'Movimientos registrados', 'value' => (string) $abonos->count(), 'tone' => 'primary'],
                ['label' => 'Total recibido', 'value' => $this->dinero($abonos->sum('monto')), 'tone' => 'success'],
                ['label' => 'Efectivo', 'value' => $this->dinero($this->sumarPorMetodo($abonos, 'efectivo')), 'tone' => 'info'],
                ['label' => 'Transferencia / tarjeta', 'value' => $this->dinero($this->sumarPorMetodo($abonos, 'transferencia') + $this->sumarPorMetodo($abonos, 'tarjeta')), 'tone' => 'warning'],
            ],
        ];
    }

    /**
     * Documentacion: Suma abonos por metodo de pago.
     * Como lo hace: Filtra la coleccion por metodo y suma montos.
     */
    private function sumarPorMetodo(Collection $abonos, string $metodo): float
    {
        return (float) $abonos->where('metodo_pago', $metodo)->sum('monto');
    }

    /**
     * Documentacion: Formatea el nombre de un paciente.
     * Como lo hace: Une nombre y apellido o devuelve texto seguro cuando falta la relacion.
     */
    private function paciente(?Paciente $paciente): string
    {
        return $paciente ? trim($paciente->nombre . ' ' . $paciente->apellido) : 'Paciente no identificado';
    }

    /**
     * Documentacion: Formatea el paciente de una venta.
     * Como lo hace: Usa consumidor final cuando la venta no pertenece a un paciente registrado.
     */
    private function pacienteVenta(?Paciente $paciente): string
    {
        return $paciente ? trim($paciente->nombre . ' ' . $paciente->apellido) : 'Consumidor final';
    }

    /**
     * Documentacion: Resume los productos de una venta.
     * Como lo hace: Une nombre y cantidad de cada detalle en una sola cadena.
     */
    private function productosVenta(VentaProducto $venta): string
    {
        if ($venta->detalles->isEmpty()) {
            return 'Sin productos';
        }

        return $venta->detalles
            ->map(fn ($detalle) => ($detalle->producto?->nombre ?? 'Producto') . ' x' . $detalle->cantidad)
            ->join(', ');
    }

    /**
     * Documentacion: Formatea el nombre de un usuario.
     * Como lo hace: Une nombre y apellido o devuelve texto de usuario no identificado.
     */
    private function usuario(?User $usuario): string
    {
        return $usuario ? trim($usuario->nombre . ' ' . $usuario->apellido) : 'Usuario no identificado';
    }

    /**
     * Documentacion: Formatea el profesional de una cita.
     * Como lo hace: Prefiere doctor externo y usa odontologa principal cuando no hay externo.
     */
    private function profesional(Cita $cita): string
    {
        if ($cita->doctorExterno) {
            return trim($cita->doctorExterno->nombre . ' ' . $cita->doctorExterno->apellido);
        }

        return 'Odontologa principal';
    }

    /**
     * Documentacion: Calcula edad para reportes.
     * Como lo hace: Parsea fecha de nacimiento con Carbon y devuelve N/A si no existe.
     */
    private function edad($fechaNacimiento): string
    {
        if (! $fechaNacimiento) {
            return 'N/A';
        }

        return (string) Carbon::parse($fechaNacimiento)->age;
    }

    /**
     * Documentacion: Formatea una fecha para mostrar.
     * Como lo hace: Parsea el valor con Carbon y devuelve formato dia/mes/anio.
     */
    private function fecha($valor): string
    {
        if (! $valor) {
            return 'N/A';
        }

        return Carbon::parse($valor)->format('d/m/Y');
    }

    /**
     * Documentacion: Formatea una hora para mostrar.
     * Como lo hace: Parsea el valor con Carbon y devuelve hora:minuto.
     */
    private function hora($valor): string
    {
        if (! $valor) {
            return 'N/A';
        }

        return Carbon::parse($valor)->format('H:i');
    }

    /**
     * Documentacion: Formatea fecha y hora para mostrar.
     * Como lo hace: Parsea el valor con Carbon y devuelve dia/mes/anio hora:minuto.
     */
    private function fechaHora($valor): string
    {
        if (! $valor) {
            return 'N/A';
        }

        return Carbon::parse($valor)->format('d/m/Y H:i');
    }

    /**
     * Documentacion: Formatea un monto monetario.
     * Como lo hace: Convierte el valor a float y lo muestra con simbolo de dolar y dos decimales.
     */
    private function dinero($valor): string
    {
        return '$' . number_format((float) $valor, 2, '.', ',');
    }

    /**
     * Documentacion: Formatea estados internos.
     * Como lo hace: Reemplaza guiones bajos y capitaliza para mostrar etiquetas legibles.
     */
    private function estado(?string $valor): string
    {
        if (! $valor) {
            return 'N/A';
        }

        return ucfirst(str_replace('_', ' ', $valor));
    }

    /**
     * Documentacion: Normaliza el origen de un reporte financiero.
     * Como lo hace: Solo acepta ventas para pagos/movimientos; el resto cae a citas.
     */
    private function normalizarOrigen(string $tipo, ?string $origen): string
    {
        if (! in_array($tipo, ['pagos', 'movimientos-pagos'], true)) {
            return 'citas';
        }

        return $origen === 'ventas' ? 'ventas' : 'citas';
    }
}
