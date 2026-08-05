<?php

namespace App\Services;

use App\Models\Paciente;
use App\Models\AntecedenteMedico;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Documentacion de clase:
 * Servicio de negocio que concentra reglas, transacciones y consultas Eloquent para mantener limpios los controladores.
 */
class PacienteService
{
    /**
     * Registra un nuevo paciente con sus antecedentes médicos
     * Equivalente a sp_registrar_paciente
     */
    /**
     * Documentacion: Registra una entidad principal del modulo.
     * Como lo hace: Valida relaciones, aplica reglas de negocio y guarda los datos en una transaccion cuando hay cambios compuestos.
     */
    public function registrar(array $datos): Paciente
    {
        return DB::transaction(function () use ($datos) {
            $paciente = Paciente::create([
                'numero_historia' => $this->generarNumeroHistoria(),
                'nombre' => $datos['nombre'],
                'apellido' => $datos['apellido'],
                'fecha_nacimiento' => $datos['fecha_nacimiento'],
                'sexo' => $datos['sexo'],
                'telefono' => $datos['telefono'] ?? null,
                'email' => $datos['email'] ?? null,
                'direccion' => $datos['direccion'] ?? null,
                'es_menor' => Carbon::parse($datos['fecha_nacimiento'])->age < 18,
                'activo' => true,
            ]);

            $this->guardarAntecedentesPaciente($paciente, $datos);

            return $paciente->fresh()->load('antecedentes');
        });
    }

    /**
     * Actualiza un paciente completo
     * Equivalente a sp_actualizar_paciente_completo
     */
    /**
     * Documentacion: Actualiza todos los datos editables de un paciente.
     * Como lo hace: Busca paciente activo, recalcula si es menor y sincroniza antecedentes medicos.
     */
    public function actualizarCompleto(int $pacienteId, array $datos): Paciente
    {
        $paciente = Paciente::where('id', $pacienteId)
            ->where('activo', true)
            ->firstOrFail();

        return DB::transaction(function () use ($paciente, $datos) {
            $paciente->update([
                'nombre' => $datos['nombre'],
                'apellido' => $datos['apellido'],
                'fecha_nacimiento' => $datos['fecha_nacimiento'],
                'sexo' => $datos['sexo'],
                'telefono' => $datos['telefono'] ?? null,
                'email' => $datos['email'] ?? null,
                'direccion' => $datos['direccion'] ?? null,
                'es_menor' => Carbon::parse($datos['fecha_nacimiento'])->age < 18,
            ]);

            $this->guardarAntecedentesPaciente($paciente, $datos);

            return $paciente->fresh()->load('antecedentes');
        });
    }

    /**
     * Guarda los antecedentes de un paciente
     */
    /**
     * Documentacion: Guarda o actualiza antecedentes medicos del paciente.
     * Como lo hace: Usa updateOrCreate para mantener un unico registro clinico por paciente.
     */
    private function guardarAntecedentesPaciente(Paciente $paciente, array $datos): void
    {
        $paciente->antecedentes()->updateOrCreate(
            ['paciente_id' => $paciente->id],
            [
                'bajo_tratamiento_medico' => $datos['bajo_tratamiento_medico'] ?? false,
                'problemas_hemorragicos' => $datos['problemas_hemorragicos'] ?? false,
                'alergias_medicamentos' => $datos['alergias_medicamentos'] ?? false,
                'detalle_alergias' => $datos['detalle_alergias'] ?? null,
                'hipertenso' => $datos['hipertenso'] ?? false,
                'diabetes' => $datos['diabetes'] ?? false,
                'embarazo' => $datos['embarazo'] ?? false,
                'motivo_consulta_inicial' => $datos['motivo_consulta_inicial'] ?? null,
            ]
        );
    }

    /**
     * Documentacion: Genera el numero de historia clinica.
     * Como lo hace: Toma el ultimo ID conocido, calcula el siguiente consecutivo y lo rellena con ceros.
     */
    private function generarNumeroHistoria(): string
    {
         $ultimoPaciente = Paciente::orderBy('id', 'desc')->first();
         $siguienteNumero = $ultimoPaciente ? $ultimoPaciente->id + 1 : 1;

        return 'HC-' . str_pad($siguienteNumero, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Actualiza los antecedentes médicos de un paciente
     * Equivalente a sp_actualizar_antecedentes
     */
    /**
     * Documentacion: Actualiza antecedentes medicos puntuales.
     * Como lo hace: Mantiene valores anteriores cuando un campo no llega en la peticion.
     */
    public function actualizarAntecedentes(int $pacienteId, array $datos): AntecedenteMedico
    {
        $paciente = Paciente::findOrFail($pacienteId);

        return DB::transaction(function () use ($paciente, $datos) {
            $antecedentes = $paciente->antecedentes;

            $antecedentes->update([
                'diabetes' => $datos['diabetes'] ?? $antecedentes->diabetes,
                'alergias_medicamentos' => $datos['alergias_medicamentos'] ?? $antecedentes->alergias_medicamentos,
                'detalle_alergias' => $datos['detalle_alergias'] ?? $antecedentes->detalle_alergias,
                'problemas_hemorragicos' => $datos['problemas_hemorragicos'] ?? $antecedentes->problemas_hemorragicos,
                'problemas_cardiacos' => $datos['problemas_cardiacos'] ?? $antecedentes->problemas_cardiacos,
                'problemas_renales' => $datos['problemas_renales'] ?? $antecedentes->problemas_renales,
                'embarazo' => $datos['embarazo'] ?? $antecedentes->embarazo,
                'otros' => $datos['otros'] ?? $antecedentes->otros,
                'presion_arterial' => $datos['presion_arterial'] ?? $antecedentes->presion_arterial,
            ]);

            return $antecedentes->fresh();
        });
    }

    /**
     * Obtiene el historial completo de un paciente
     * Equivalente a sp_obtener_historial_paciente
     */
    /**
     * Documentacion: Obtiene historial clinico o financiero relacionado.
     * Como lo hace: Carga relaciones necesarias en una sola consulta compuesta para mostrar el detalle completo.
     */
    public function obtenerHistorial(int $pacienteId)
    {
        $paciente = Paciente::with([
            'antecedentes',
            'citas' => function ($query) {
                $query->with([
                    'diagnostico.dientes',
                    'tratamientos.tipoTratamiento',
                    'pago.abonos',
                    'usuario',
                    'doctorExterno'
                ])->orderBy('fecha_hora_inicio', 'desc');
            }
        ])->findOrFail($pacienteId);

        return $paciente;
    }

    /**
     * Obtiene el saldo total de un paciente
     */
    /**
     * Documentacion: Calcula saldo total pendiente.
     * Como lo hace: Suma saldos de pagos pendientes o parciales relacionados con el paciente.
     */
    public function obtenerSaldoTotal(int $pacienteId): float
    {
        $paciente = Paciente::findOrFail($pacienteId);
        return $paciente->obtenerSaldoTotal();
    }

    /**
     * Lista de pacientes con deuda
     */
    /**
     * Documentacion: Lista pacientes con deuda.
     * Como lo hace: Consulta la vista SQL de deudores y pagina el resultado si se solicita.
     */
    public function obtenerDeudores($paginated = true)
    {
        $query = DB::table('vw_pacientes_deudores');

        if ($paginated) {
            return $query->paginate(15);
        }

        return $query->get();
    }

    /**
     * Desactiva un paciente (soft delete mediante bandera activo)
     */
    /**
     * Documentacion: Desactiva un registro sin borrarlo fisicamente.
     * Como lo hace: Actualiza la bandera activo para conservar el historial asociado.
     */
    public function desactivar(int $pacienteId): bool
    {
        $paciente = Paciente::findOrFail($pacienteId);
        return $paciente->update(['activo' => false]);
    }

    /**
     * Activa un paciente
     */
    /**
     * Documentacion: Activa nuevamente un registro.
     * Como lo hace: Actualiza la bandera activo para que vuelva a aparecer en listados operativos.
     */
    public function activar(int $pacienteId): bool
    {
        $paciente = Paciente::findOrFail($pacienteId);
        return $paciente->update(['activo' => true]);
    }

    /**
     * Busca pacientes por nombre, apellido o número de historia
     */
    /**
     * Documentacion: Busca registros por texto.
     * Como lo hace: Aplica condiciones LIKE sobre los campos mas utiles del modulo y pagina la respuesta.
     */
    public function buscar(string $termino, $paginated = true)
    {
        $query = Paciente::where('activo', true)
            ->where(function ($q) use ($termino) {
                $q->where('nombre', 'like', "%{$termino}%")
                  ->orWhere('apellido', 'like', "%{$termino}%")
                  ->orWhere('numero_historia', 'like', "%{$termino}%")
                  ->orWhere('telefono', 'like', "%{$termino}%")
                  ->orWhere('email', 'like', "%{$termino}%");
            });

        if ($paginated) {
            return $query->paginate(15);
        }

        return $query->get();
    }
}
