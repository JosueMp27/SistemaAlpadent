<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Tratamiento\Repositories\TratamientoRepositoryInterface;
use App\Models\TratamientoRealizado;
use App\Models\Cita;
use App\Models\TipoTratamiento;
use Illuminate\Support\Facades\DB;

class EloquentTratamientoRepository implements TratamientoRepositoryInterface
{
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

    public function eliminar(int $tratamientoId): bool
    {
        $tratamiento = TratamientoRealizado::findOrFail($tratamientoId);
        return $tratamiento->delete();
    }

    public function obtenerPorCita(int $citaId)
    {
        return TratamientoRealizado::where('cita_id', $citaId)
            ->with(['tipoTratamiento', 'diagnostico'])
            ->get();
    }

    public function calcularTotalCita(int $citaId): float
    {
        return TratamientoRealizado::where('cita_id', $citaId)
            ->sum('precio_aplicado');
    }

    public function obtenerTratamientosFrequentes($fechaInicio, $fechaFin)
    {
        return DB::select(
            'CALL sp_reporte_tratamientos_frecuentes(?, ?)',
            [$fechaInicio, $fechaFin]
        );
    }

    public function obtenerRendimiento()
    {
        return DB::table('vw_rendimiento_tratamientos')->get();
    }

    public function obtenerTiposDisponibles($categoria = null)
    {
        $query = TipoTratamiento::where('activo', true);

        if ($categoria) {
            $query->where('categoria', $categoria);
        }

        return $query->orderBy('categoria', 'asc')->get();
    }

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

    public function obtenerCategorias()
    {
        return TipoTratamiento::where('activo', true)
            ->distinct()
            ->pluck('categoria')
            ->sort()
            ->values();
    }
}
