<?php

/**
 * Documentacion de archivo:
 * Controlador API que recibe peticiones HTTP, valida entradas y delega en la capa de aplicacion Hexagonal.
 */

namespace App\Http\Controllers\Api;

use App\Application\Reportes\Services\ReportesApplicationService;
use App\Services\SimplePdfService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class ReporteController extends ApiController
{
    public function __construct(
        private readonly ReportesApplicationService $reportesService,
        private readonly SimplePdfService $pdfService
    ) {}

    public function catalogo()
    {
        return $this->successResponse(
            array_values($this->reportesService->catalogo()),
            'Catalogo de reportes obtenido correctamente'
        );
    }

    public function show(Request $request, string $tipo)
    {
        try {
            $reporte = $this->reportesService->obtener($tipo, $request->user(), $this->opciones($request));

            return $this->successResponse($reporte, 'Reporte generado correctamente');
        } catch (InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function pdf(Request $request, string $tipo)
    {
        try {
            $reporte = $this->reportesService->obtener($tipo, $request->user(), $this->opciones($request));
            $pdf = $this->pdfService->generarReporte($reporte);
            $filename = $this->nombreArchivo($tipo, 'pdf');

            return response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function excel(Request $request, string $tipo)
    {
        try {
            $reporte = $this->reportesService->obtener($tipo, $request->user(), $this->opciones($request));
            $html = $this->generarExcelHtml($reporte);
            $filename = $this->nombreArchivo($tipo, 'xls');

            return response("\xEF\xBB\xBF" . $html, 200, [
                'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        } catch (InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    private function generarExcelHtml(array $reporte): string
    {
        $html = '<html><head><meta charset="UTF-8"></head><body>';
        $html .= '<h2>ALPADENT - REPORTES</h2>';
        $html .= '<h3>' . e($reporte['titulo']) . '</h3>';
        $html .= '<p><strong>Reporte generado por:</strong> ' . e($reporte['generado_por']) . '</p>';
        $html .= '<p><strong>Fecha y hora:</strong> ' . e($reporte['generado_en']) . '</p>';
        $html .= '<p><strong>Total de registros:</strong> ' . e((string) $reporte['total_registros']) . '</p>';

        $html .= '<table border="1" cellpadding="6" cellspacing="0">';
        $html .= '<thead><tr style="background:#dbeafe;font-weight:bold;">';

        foreach ($reporte['columnas'] as $columna) {
            $html .= '<th>' . e($columna) . '</th>';
        }

        $html .= '</tr></thead><tbody>';

        foreach ($reporte['filas'] as $fila) {
            $html .= '<tr>';

            foreach ($reporte['columnas'] as $columna) {
                $html .= '<td>' . e((string) ($fila[$columna] ?? '')) . '</td>';
            }

            $html .= '</tr>';
        }

        if (empty($reporte['filas'])) {
            $html .= '<tr><td colspan="' . count($reporte['columnas']) . '">No hay registros para mostrar.</td></tr>';
        }

        $html .= '</tbody></table></body></html>';

        return $html;
    }

    private function nombreArchivo(string $tipo, string $extension): string
    {
        return 'alpadent_reporte_' . str_replace('-', '_', $tipo) . '_' . now()->format('Ymd_His') . '.' . $extension;
    }

    private function opciones(Request $request): array
    {
        return [
            'origen' => $request->query('origen', 'citas'),
        ];
    }
}
