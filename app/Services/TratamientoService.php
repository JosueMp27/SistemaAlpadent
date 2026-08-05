<?php

namespace App\Services;

use App\Models\TratamientoRealizado;
use App\Models\Cita;
use App\Models\TipoTratamiento;
use Illuminate\Support\Facades\DB;

/**
 * Documentacion de clase:
 * Servicio de negocio que concentra reglas, transacciones y consultas Eloquent para mantener limpios los controladores.
 */
class TratamientoService
{
    /**
     * Registra un tratamiento realizado en una cita
     * Equivalente a sp_registrar_tratamiento
     */
    /**
     * Documentacion: Registra una entidad principal del modulo.
     * Como lo hace: Valida relaciones, aplica reglas de negocio y guarda los datos en una transaccion cuando hay cambios compuestos.
     */
    public function registrar(array $datos): TratamientoRealizado
    {
        $cita = Cita::where('id', $datos['cita_id'])
            ->whereIn('estado', ['programada', 'en_curso'])
            ->firstOrFail();

        $tipoTratamiento = TipoTratamiento::where('id', $datos['tipo_tratamiento_id'])
            ->where('activo', true)
            ->firstOrFail();

        return DB::transaction(function () use ($datos, $tipoTratamiento) {
            $precioAplicado = $datos['precio_aplicado'] ?? $tipoTratamiento->precio;

            $tratamiento = TratamientoRealizado::create([
                'cita_id' => $datos['cita_id'],
                'tipo_tratamiento_id' => $datos['tipo_tratamiento_id'],
                'diagnostico_id' => $datos['diagnostico_id'] ?? null,
                'numero_diente' => $datos['numero_diente'] ?? null,
                'precio_aplicado' => $precioAplicado,
                'notas' => $datos['notas'] ?? null,
            ]);

            return $tratamiento->fresh()->load('tipoTratamiento', 'cita', 'diagnostico');
        });
    }

    /**
     * Actualiza un tratamiento realizado
     */
    /**
     * Documentacion: Ejecuta la operacion actualizar.
     * Como lo hace: Coordina modelos Eloquent, calculos y transacciones para aplicar una regla de negocio.
     */
    public function actualizar(int $tratamientoId, array $datos): TratamientoRealizado
    {
        $tratamiento = TratamientoRealizado::findOrFail($tratamientoId);

        return DB::transaction(function () use ($tratamiento, $datos) {
            $tratamiento->update([
                'precio_aplicado' => $datos['precio_aplicado'] ?? $tratamiento->precio_aplicado,
                'notas' => $datos['notas'] ?? $tratamiento->notas,
                'numero_diente' => $datos['numero_diente'] ?? $tratamiento->numero_diente,
            ]);

            return $tratamiento->fresh();
        });
    }

    /**
     * Elimina un tratamiento registrado
     */
    /**
     * Documentacion: Ejecuta la operacion eliminar.
     * Como lo hace: Coordina modelos Eloquent, calculos y transacciones para aplicar una regla de negocio.
     */
    public function eliminar(int $tratamientoId): bool
    {
        $tratamiento = TratamientoRealizado::findOrFail($tratamientoId);
        return $tratamiento->delete();
    }

    /**
     * Obtiene tratamientos de una cita
     */
    /**
     * Documentacion: Genera un reporte solicitado.
     * Como lo hace: Valida el tipo, calcula filas y resumen, y agrega metadatos de usuario y fecha.
     */
    public function obtenerPorCita(int $citaId)
    {
        return TratamientoRealizado::where('cita_id', $citaId)
            ->with(['tipoTratamiento', 'diagnostico'])
            ->get();
    }

    /**
     * Calcula el total de tratamientos de una cita
     */
    /**
     * Documentacion: Calcula el total de tratamientos de una cita.
     * Como lo hace: Suma los precios aplicados de los tratamientos registrados en esa cita.
     */
    public function calcularTotalCita(int $citaId): float
    {
        return TratamientoRealizado::where('cita_id', $citaId)
            ->sum('precio_aplicado');
    }

    /**
     * Obtiene tratamientos más frecuentes
     */
    /**
     * Documentacion: Genera un reporte solicitado.
     * Como lo hace: Valida el tipo, calcula filas y resumen, y agrega metadatos de usuario y fecha.
     */
    public function obtenerTratamientosFrequentes($fechaInicio, $fechaFin)
    {
        return DB::select(
            'CALL sp_reporte_tratamientos_frecuentes(?, ?)',
            [$fechaInicio, $fechaFin]
        );
    }

    /**
     * Obtiene rendimiento de tratamientos
     */
    /**
     * Documentacion: Genera un reporte solicitado.
     * Como lo hace: Valida el tipo, calcula filas y resumen, y agrega metadatos de usuario y fecha.
     */
    public function obtenerRendimiento()
    {
        return DB::table('vw_rendimiento_tratamientos')->get();
    }

    /**
     * Obtiene tipos de tratamiento disponibles
     */
    /**
     * Documentacion: Lista tratamientos disponibles.
     * Como lo hace: Filtra tratamientos activos y opcionalmente por categoria.
     */
    public function obtenerTiposDisponibles($categoria = null)
    {
        $query = TipoTratamiento::where('activo', true);

        if ($categoria) {
            $query->where('categoria', $categoria);
        }

        return $query->orderBy('categoria', 'asc')->get();
    }

    /**
     * Obtiene estadísticas de tratamientos
     */
    /**
     * Documentacion: Calcula estadisticas del modulo.
     * Como lo hace: Usa conteos, sumas o vistas SQL para devolver indicadores listos para tarjetas o reportes.
     */
    public function obtenerEstadisticas()
    {
        return [
            'total_tratamientos' => TratamientoRealizado::count(),
            'total_generado' => TratamientoRealizado::sum('precio_aplicado'),
            'promedio_precio' => TratamientoRealizado::avg('precio_aplicado') ?? 0,
            'por_categoria' => DB::table('vw_rendimiento_tratamientos')
                ->selectRaw('categoria, SUM(cantidad_realizada) as total')
                ->groupBy('categoria')
                ->get(),
        ];
    }

    /**
     * Obtiene categorías de tratamientos
     */
    /**
     * Documentacion: Obtiene categorias disponibles.
     * Como lo hace: Extrae categorias distintas de tratamientos activos y las ordena.
     */
    public function obtenerCategorias()
    {
        return TipoTratamiento::where('activo', true)
            ->distinct()
            ->pluck('categoria')
            ->sort()
            ->values();
    }
}
