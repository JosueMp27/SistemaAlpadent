<?php

namespace App\Services;

use App\Models\Cita;
use App\Models\Odontograma;
use App\Models\OdontogramaCondicion;
use App\Models\OdontogramaMarca;
use App\Models\Paciente;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Documentacion de clase:
 * Servicio de negocio que concentra reglas, transacciones y consultas Eloquent para mantener limpios los controladores.
 */
class OdontogramaService
{
    public const DIENTES_PERMANENTES = [
        18, 17, 16, 15, 14, 13, 12, 11,
        21, 22, 23, 24, 25, 26, 27, 28,
        48, 47, 46, 45, 44, 43, 42, 41,
        31, 32, 33, 34, 35, 36, 37, 38,
    ];

    public const DIENTES_TEMPORALES = [
        55, 54, 53, 52, 51,
        61, 62, 63, 64, 65,
        85, 84, 83, 82, 81,
        71, 72, 73, 74, 75,
    ];

    public const SUPERFICIES = [
        'general',
        'oclusal',
        'vestibular',
        'lingual',
        'mesial',
        'distal',
    ];

    public const CONDICIONES = [
        'sano' => ['label' => 'Sano / limpiar', 'color' => '#ffffff', 'grupo' => 'neutro'],
        'cariado' => ['label' => 'Caries', 'color' => '#ef4444', 'grupo' => 'cpo_c'],
        'obturado' => ['label' => 'Obturado', 'color' => '#2563eb', 'grupo' => 'cpo_o'],
        'extraccion_indicada' => ['label' => 'Extraccion indicada', 'color' => '#dc2626', 'grupo' => 'cpo_p'],
        'perdido' => ['label' => 'Perdido / ausente', 'color' => '#374151', 'grupo' => 'cpo_p'],
        'endodoncia' => ['label' => 'Endodoncia', 'color' => '#7c3aed', 'grupo' => 'tratamiento'],
        'corona' => ['label' => 'Corona', 'color' => '#d4a017', 'grupo' => 'tratamiento'],
        'puente' => ['label' => 'Puente', 'color' => '#f97316', 'grupo' => 'tratamiento'],
        'implante' => ['label' => 'Implante', 'color' => '#0f766e', 'grupo' => 'tratamiento'],
        'sellante' => ['label' => 'Sellante', 'color' => '#22c55e', 'grupo' => 'tratamiento'],
        'fractura' => ['label' => 'Fractura', 'color' => '#b91c1c', 'grupo' => 'hallazgo'],
        'tratamiento_indicado' => ['label' => 'Tratamiento indicado', 'color' => '#facc15', 'grupo' => 'hallazgo'],
    ];

    /**
     * Documentacion: Obtiene el odontograma de un paciente.
     * Como lo hace: Busca paciente, crea odontograma si falta y devuelve marcas, indices, catalogos y citas.
     */
    public function obtenerPorPaciente(int $pacienteId, ?int $usuarioId = null): array
    {
        $paciente = Paciente::with('antecedentes')->findOrFail($pacienteId);
        $odontograma = $this->obtenerOCrearOdontograma($pacienteId, $usuarioId);
        $odontograma->load(['marcas.tipoTratamiento', 'marcas.usuario', 'usuario']);

        return $this->formatearRespuesta($odontograma, $paciente);
    }

    /**
     * Documentacion: Guarda una marca dental en el odontograma.
     * Como lo hace: Valida diente, resuelve denticion, limpia marcas incompatibles y recalcula indices CPO/ceo.
     */
    public function guardarMarca(int $pacienteId, array $datos): array
    {
        return DB::transaction(function () use ($pacienteId, $datos) {
            $odontograma = $this->obtenerOCrearOdontograma($pacienteId, $datos['usuario_id'] ?? null);
            $numeroDiente = (int) $datos['numero_diente'];
            $this->validarDiente($numeroDiente);

            $superficie = $datos['superficie'] ?? 'general';
            $condicion = $datos['condicion'];
            $denticion = $this->obtenerDenticion($numeroDiente);

            if ($condicion === 'sano') {
                OdontogramaMarca::where('odontograma_id', $odontograma->id)
                    ->where('numero_diente', $numeroDiente)
                    ->where('superficie', $superficie)
                    ->delete();
            } else {
                if (in_array($condicion, ['perdido', 'extraccion_indicada'], true)) {
                    $superficie = 'general';
                    OdontogramaMarca::where('odontograma_id', $odontograma->id)
                        ->where('numero_diente', $numeroDiente)
                        ->delete();
                } elseif ($superficie !== 'general') {
                    OdontogramaMarca::where('odontograma_id', $odontograma->id)
                        ->where('numero_diente', $numeroDiente)
                        ->where('superficie', 'general')
                        ->whereIn('condicion', ['perdido', 'extraccion_indicada'])
                        ->delete();
                }

                OdontogramaMarca::updateOrCreate(
                    [
                        'odontograma_id' => $odontograma->id,
                        'numero_diente' => $numeroDiente,
                        'superficie' => $superficie,
                    ],
                    [
                        'cita_id' => $datos['cita_id'] ?? null,
                        'tipo_tratamiento_id' => $datos['tipo_tratamiento_id'] ?? null,
                        'usuario_id' => $datos['usuario_id'] ?? $odontograma->usuario_id,
                        'denticion' => $denticion,
                        'condicion' => $condicion,
                        'color' => $this->colorCondicion($condicion),
                        'observacion' => $datos['observacion'] ?? null,
                    ]
                );
            }

            $this->recalcularIndices($odontograma);

            return $this->obtenerPorPaciente($pacienteId, $datos['usuario_id'] ?? null);
        });
    }

    /**
     * Documentacion: Actualiza indicadores generales del odontograma.
     * Como lo hace: Guarda higiene, enfermedad periodontal, maloclusion, fluorosis y observaciones.
     */
    public function actualizarIndicadores(int $pacienteId, array $datos): array
    {
        return DB::transaction(function () use ($pacienteId, $datos) {
            $odontograma = $this->obtenerOCrearOdontograma($pacienteId, $datos['usuario_id'] ?? null);

            $odontograma->update([
                'usuario_id' => $datos['usuario_id'] ?? $odontograma->usuario_id,
                'higiene_placa' => $datos['higiene_placa'] ?? null,
                'higiene_calculo' => $datos['higiene_calculo'] ?? null,
                'higiene_gingivitis' => $datos['higiene_gingivitis'] ?? null,
                'enfermedad_periodontal' => $datos['enfermedad_periodontal'] ?? 'ninguna',
                'maloclusion' => $datos['maloclusion'] ?? 'ninguna',
                'fluorosis' => $datos['fluorosis'] ?? 'ninguna',
                'observaciones' => $datos['observaciones'] ?? null,
            ]);

            return $this->obtenerPorPaciente($pacienteId, $datos['usuario_id'] ?? null);
        });
    }

    /**
     * Documentacion: Elimina una marca del odontograma.
     * Como lo hace: Borra la marca, recalcula indices y devuelve el odontograma actualizado.
     */
    public function eliminarMarca(int $marcaId): array
    {
        return DB::transaction(function () use ($marcaId) {
            $marca = OdontogramaMarca::with('odontograma')->findOrFail($marcaId);
            $pacienteId = $marca->odontograma->paciente_id;
            $odontograma = $marca->odontograma;

            $marca->delete();
            $this->recalcularIndices($odontograma);

            return $this->obtenerPorPaciente($pacienteId);
        });
    }

    /**
     * Documentacion: Entrega catalogos del odontograma.
     * Como lo hace: Devuelve superficies, condiciones y listas de dientes permanentes y temporales.
     */
    public function obtenerCatalogos(): array
    {
        return [
            'superficies' => self::SUPERFICIES,
            'condiciones' => $this->obtenerCondiciones(),
            'dientes' => [
                'permanentes' => self::DIENTES_PERMANENTES,
                'temporales' => self::DIENTES_TEMPORALES,
            ],
        ];
    }

    /**
     * Documentacion: Obtiene claves de condiciones activas.
     * Como lo hace: Lee el catalogo vigente para construir reglas de validacion dinamicas.
     */
    public function obtenerClavesCondicionesActivas(): array
    {
        return array_keys($this->obtenerCondiciones());
    }

    /**
     * Documentacion: Obtiene o crea el odontograma base.
     * Como lo hace: Confirma que el paciente exista y usa firstOrCreate para tener un registro unico.
     */
    private function obtenerOCrearOdontograma(int $pacienteId, ?int $usuarioId = null): Odontograma
    {
        Paciente::findOrFail($pacienteId);

        return Odontograma::firstOrCreate(
            ['paciente_id' => $pacienteId],
            ['usuario_id' => $usuarioId]
        );
    }

    /**
     * Documentacion: Da formato a la respuesta del odontograma.
     * Como lo hace: Transforma marcas Eloquent en arreglos listos para pintar dientes y paneles.
     */
    private function formatearRespuesta(Odontograma $odontograma, Paciente $paciente): array
    {
        $odontograma->loadMissing(['marcas.tipoTratamiento', 'marcas.usuario']);

        $condiciones = $this->obtenerCondiciones(false);

        $marcas = $odontograma->marcas->map(function (OdontogramaMarca $marca) use ($condiciones) {
            return [
                'id' => $marca->id,
                'numero_diente' => $marca->numero_diente,
                'denticion' => $marca->denticion,
                'superficie' => $marca->superficie,
                'condicion' => $marca->condicion,
                'condicion_label' => $condiciones[$marca->condicion]['label'] ?? $marca->condicion,
                'color' => $marca->color ?: ($condiciones[$marca->condicion]['color'] ?? '#94a3b8'),
                'observacion' => $marca->observacion,
                'cita_id' => $marca->cita_id,
                'tipo_tratamiento_id' => $marca->tipo_tratamiento_id,
                'tratamiento' => $marca->tipoTratamiento?->nombre,
                'usuario' => $marca->usuario
                    ? trim($marca->usuario->nombre . ' ' . $marca->usuario->apellido)
                    : null,
                'updated_at' => optional($marca->updated_at)->format('Y-m-d H:i:s'),
            ];
        })->values();

        return [
            'paciente' => $paciente,
            'odontograma' => $odontograma,
            'marcas' => $marcas,
            'marcas_por_diente' => $marcas->groupBy('numero_diente'),
            'indices' => $this->obtenerIndices($odontograma),
            'catalogos' => $this->obtenerCatalogos(),
            'citas' => $this->obtenerCitasPaciente($paciente->id),
        ];
    }

    /**
     * Documentacion: Lista citas de un paciente para el odontograma.
     * Como lo hace: Carga tratamiento, excluye canceladas y devuelve datos simples de fecha, hora y motivo.
     */
    private function obtenerCitasPaciente(int $pacienteId): Collection
    {
        return Cita::with('tipoTratamiento')
            ->where('paciente_id', $pacienteId)
            ->whereNotIn('estado', ['cancelada', 'no_asistio'])
            ->orderBy('fecha_hora_inicio', 'desc')
            ->get()
            ->map(function (Cita $cita) {
                return [
                    'id' => $cita->id,
                    'fecha' => optional($cita->fecha_hora_inicio)->format('Y-m-d'),
                    'hora' => optional($cita->fecha_hora_inicio)->format('H:i'),
                    'motivo' => $cita->motivo_consulta,
                    'tratamiento' => $cita->tipoTratamiento?->nombre,
                    'tipo_tratamiento_id' => $cita->tipo_tratamiento_id,
                ];
            });
    }

    /**
     * Documentacion: Recalcula indices odontologicos.
     * Como lo hace: Lee marcas actuales, calcula CPO/ceo y actualiza los totales guardados.
     */
    private function recalcularIndices(Odontograma $odontograma): void
    {
        $marcas = $odontograma->marcas()->get();
        $indices = $this->calcularIndices($marcas);

        $odontograma->update([
            'indice_cpo_cariados' => $indices['cpo']['cariados'],
            'indice_cpo_perdidos' => $indices['cpo']['perdidos'],
            'indice_cpo_obturados' => $indices['cpo']['obturados'],
            'indice_ceo_cariados' => $indices['ceo']['cariados'],
            'indice_ceo_extraidos' => $indices['ceo']['extraidos'],
            'indice_ceo_obturados' => $indices['ceo']['obturados'],
        ]);
    }

    /**
     * Documentacion: Calcula indices CPO y ceo.
     * Como lo hace: Separa dientes permanentes y temporales, clasifica marcas y suma cariados, perdidos/extraidos y obturados.
     */
    private function calcularIndices(Collection $marcas): array
    {
        $permanentes = $this->clasificarDientesPorIndice(
            $marcas->where('denticion', 'permanente')
        );
        $temporales = $this->clasificarDientesPorIndice(
            $marcas->where('denticion', 'temporal')
        );

        return [
            'cpo' => [
                'cariados' => $permanentes['cariados'],
                'perdidos' => $permanentes['perdidos'],
                'obturados' => $permanentes['obturados'],
                'total' => $permanentes['cariados'] + $permanentes['perdidos'] + $permanentes['obturados'],
            ],
            'ceo' => [
                'cariados' => $temporales['cariados'],
                'extraidos' => $temporales['perdidos'],
                'obturados' => $temporales['obturados'],
                'total' => $temporales['cariados'] + $temporales['perdidos'] + $temporales['obturados'],
            ],
        ];
    }

    /**
     * Documentacion: Clasifica dientes para indices odontologicos.
     * Como lo hace: Agrupa marcas por diente y aplica prioridad perdido, cariado y obturado para no contar doble.
     */
    private function clasificarDientesPorIndice(Collection $marcas): array
    {
        $resultado = ['cariados' => 0, 'perdidos' => 0, 'obturados' => 0];

        $grupos = collect($this->obtenerCondiciones(false))
            ->mapWithKeys(fn ($item, $clave) => [$clave => $item['grupo'] ?? 'hallazgo']);

        $marcas->groupBy('numero_diente')->each(function (Collection $marcasDiente) use (&$resultado, $grupos) {
            $condiciones = $marcasDiente->pluck('condicion')->unique();
            $gruposDiente = $condiciones->map(fn ($condicion) => $grupos[$condicion] ?? 'hallazgo');

            if ($gruposDiente->contains('cpo_p')) {
                $resultado['perdidos']++;
                return;
            }

            if ($gruposDiente->contains('cpo_c')) {
                $resultado['cariados']++;
                return;
            }

            if ($gruposDiente->contains('cpo_o')) {
                $resultado['obturados']++;
            }
        });

        return $resultado;
    }

    /**
     * Documentacion: Devuelve indices ya guardados.
     * Como lo hace: Empaqueta valores CPO y ceo junto con sus totales calculados.
     */
    private function obtenerIndices(Odontograma $odontograma): array
    {
        return [
            'cpo' => [
                'cariados' => $odontograma->indice_cpo_cariados,
                'perdidos' => $odontograma->indice_cpo_perdidos,
                'obturados' => $odontograma->indice_cpo_obturados,
                'total' => $odontograma->indice_cpo_cariados + $odontograma->indice_cpo_perdidos + $odontograma->indice_cpo_obturados,
            ],
            'ceo' => [
                'cariados' => $odontograma->indice_ceo_cariados,
                'extraidos' => $odontograma->indice_ceo_extraidos,
                'obturados' => $odontograma->indice_ceo_obturados,
                'total' => $odontograma->indice_ceo_cariados + $odontograma->indice_ceo_extraidos + $odontograma->indice_ceo_obturados,
            ],
        ];
    }

    /**
     * Documentacion: Valida que el numero dental exista.
     * Como lo hace: Comprueba el numero contra listas FDI permanentes y temporales.
     */
    private function validarDiente(int $numeroDiente): void
    {
        if (! in_array($numeroDiente, array_merge(self::DIENTES_PERMANENTES, self::DIENTES_TEMPORALES), true)) {
            throw new \Exception('El numero de diente no pertenece al odontograma.');
        }
    }

    /**
     * Documentacion: Determina si un diente es temporal o permanente.
     * Como lo hace: Busca el numero en la lista temporal y usa permanente como valor por defecto.
     */
    private function obtenerDenticion(int $numeroDiente): string
    {
        return in_array($numeroDiente, self::DIENTES_TEMPORALES, true) ? 'temporal' : 'permanente';
    }

    /**
     * Documentacion: Obtiene condiciones configurables del odontograma.
     * Como lo hace: Lee opciones desde base de datos y usa un catalogo fijo si aun no hay registros.
     */
    private function obtenerCondiciones(bool $soloActivas = true): array
    {
        $query = OdontogramaCondicion::query();

        if ($soloActivas) {
            $query->where('activo', true);
        }

        $condiciones = $query->orderBy('grupo')->orderBy('label')->get();

        if ($condiciones->isEmpty()) {
            return self::CONDICIONES;
        }

        return $condiciones
            ->mapWithKeys(fn (OdontogramaCondicion $condicion) => [
                $condicion->clave => [
                    'label' => $condicion->label,
                    'color' => $condicion->color,
                    'grupo' => $condicion->grupo,
                ],
            ])
            ->all();
    }

    /**
     * Documentacion: Resuelve el color de una condicion dental.
     * Como lo hace: Consulta el catalogo vigente y cae al catalogo fijo o a un gris neutro.
     */
    private function colorCondicion(string $condicion): string
    {
        return $this->obtenerCondiciones(false)[$condicion]['color']
            ?? self::CONDICIONES[$condicion]['color']
            ?? '#94a3b8';
    }
}
