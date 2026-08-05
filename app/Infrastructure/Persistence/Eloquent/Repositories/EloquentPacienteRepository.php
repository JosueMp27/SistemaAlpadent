<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Paciente\Repositories\PacienteRepositoryInterface;
use App\Models\Paciente;
use App\Models\AntecedenteMedico;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EloquentPacienteRepository implements PacienteRepositoryInterface
{
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

    public function obtenerHistorial(int $pacienteId)
    {
        return Paciente::with([
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
    }

    public function obtenerSaldoTotal(int $pacienteId): float
    {
        $paciente = Paciente::findOrFail($pacienteId);
        return $paciente->obtenerSaldoTotal();
    }

    public function obtenerDeudores(bool $paginated = true)
    {
        $query = DB::table('vw_pacientes_deudores');

        if ($paginated) {
            return $query->paginate(15);
        }

        return $query->get();
    }

    public function desactivar(int $pacienteId): bool
    {
        $paciente = Paciente::findOrFail($pacienteId);
        return $paciente->update(['activo' => false]);
    }

    public function activar(int $pacienteId): bool
    {
        $paciente = Paciente::findOrFail($pacienteId);
        return $paciente->update(['activo' => true]);
    }

    public function buscar(string $termino, bool $paginated = true)
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

    public function obtenerPorId(int $pacienteId): Paciente
    {
        return Paciente::with('antecedentes')->findOrFail($pacienteId);
    }

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

    private function generarNumeroHistoria(): string
    {
        $ultimoPaciente = Paciente::orderBy('id', 'desc')->first();
        $siguienteNumero = $ultimoPaciente ? $ultimoPaciente->id + 1 : 1;

        return 'HC-' . str_pad($siguienteNumero, 6, '0', STR_PAD_LEFT);
    }
}
