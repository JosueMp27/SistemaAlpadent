<?php

namespace App\Application\Reportes\Services;

use App\Services\ReportesService;
use App\Models\User;

class ReportesApplicationService
{
    private ReportesService $reportesService;

    public function __construct(?ReportesService $reportesService = null)
    {
        $this->reportesService = $reportesService ?? new ReportesService();
    }

    public function catalogo(): array
    {
        return $this->reportesService->catalogo();
    }

    public function obtener(string $tipo, ?User $usuario = null, array $opciones = []): array
    {
        return $this->reportesService->obtener($tipo, $usuario, $opciones);
    }
}
