<?php

namespace App\Domain\Diagnostico\Repositories;

use App\Models\Diagnostico;
use App\Models\DienteDiagnostico;

interface DiagnosticoRepositoryInterface
{
    public function registrar(array $datos): Diagnostico;
    public function agregarDiente(int $diagnosticoId, array $datos): DienteDiagnostico;
    public function actualizarDiente(int $dienteId, array $datos): DienteDiagnostico;
    public function eliminarDiente(int $dienteId): bool;
    public function obtenerPorCita(int $citaId);
    public function obtenerOdontograma(int $diagnosticoId);
    public function obtenerRecientes(int $limite = 10);
    public function obtenerHistorialPaciente(int $pacienteId): array;
    public function obtenerEstadisticas();
}
