<?php

namespace App\Domain\Paciente\Repositories;

use App\Models\Paciente;
use App\Models\AntecedenteMedico;

interface PacienteRepositoryInterface
{
    public function registrar(array $datos): Paciente;
    public function actualizarCompleto(int $pacienteId, array $datos): Paciente;
    public function actualizarAntecedentes(int $pacienteId, array $datos): AntecedenteMedico;
    public function obtenerHistorial(int $pacienteId);
    public function obtenerSaldoTotal(int $pacienteId): float;
    public function obtenerDeudores(bool $paginated = true);
    public function desactivar(int $pacienteId): bool;
    public function activar(int $pacienteId): bool;
    public function buscar(string $termino, bool $paginated = true);
    public function obtenerPorId(int $pacienteId): Paciente;
}
