<?php

namespace App\Services;

/**
 * Documentacion de clase:
 * Servicio de negocio que concentra reglas, transacciones y consultas Eloquent para mantener limpios los controladores.
 */
class SimplePdfService
{
    private const PAGE_WIDTH = 842;
    private const PAGE_HEIGHT = 595;
    private const MARGIN = 36;
    private const TABLE_FONT_SIZE = 6;
    private const HEADER_FONT_SIZE = 6;
    private const CELL_PADDING = 3;

    private array $pages = [];
    private array $current = [];
    private float $y = 0;

    /**
     * Documentacion: Genera un PDF de reporte.
     * Como lo hace: Inicializa paginas, dibuja encabezado, resumen y tabla, y construye el binario PDF.
     */
    public function generarReporte(array $reporte): string
    {
        $this->pages = [];
        $this->startPage();

        $this->drawHeader($reporte);
        $this->drawSummary($reporte['resumen']);
        $this->drawTable($reporte['columnas'], $reporte['filas']);

        $this->finishPage();

        return $this->buildPdf();
    }

    /**
     * Documentacion: Dibuja el encabezado del PDF.
     * Como lo hace: Agrega fondo, titulo, usuario, fecha y total de registros con comandos PDF.
     */
    private function drawHeader(array $reporte): void
    {
        $this->fillRect(self::MARGIN, self::PAGE_HEIGHT - 112, self::PAGE_WIDTH - (self::MARGIN * 2), 76, 'EEF6FF');
        $this->lineRect(self::MARGIN, self::PAGE_HEIGHT - 112, self::PAGE_WIDTH - (self::MARGIN * 2), 76, 'BFD7F2');

        $this->y = self::PAGE_HEIGHT - 55;
        $this->textAt('ALPADENT - REPORTES', self::MARGIN + 14, $this->y, 15, true, '0D4F8B');
        $this->y -= 20;
        $this->textAt($reporte['titulo'], self::MARGIN + 14, $this->y, 12, true, '172033');

        $this->textAt('Reporte generado por: ' . $reporte['generado_por'], 548, self::PAGE_HEIGHT - 58, 8, false, '334155');
        $this->textAt('Fecha y hora: ' . $reporte['generado_en'], 548, self::PAGE_HEIGHT - 72, 8, false, '334155');
        $this->textAt('Total de registros: ' . $reporte['total_registros'], 548, self::PAGE_HEIGHT - 86, 8, true, '334155');

        $this->y = self::PAGE_HEIGHT - 128;
    }

    /**
     * Documentacion: Dibuja tarjetas de resumen del PDF.
     * Como lo hace: Calcula anchos, pinta tarjetas y prepara la posicion vertical para la tabla.
     */
    private function drawSummary(array $resumen): void
    {
        $availableWidth = self::PAGE_WIDTH - (self::MARGIN * 2);
        $gap = 8;
        $cardWidth = ($availableWidth - ($gap * 3)) / 4;
        $x = self::MARGIN;

        foreach (array_slice($resumen, 0, 4) as $item) {
            $this->fillRect($x, $this->y - 42, $cardWidth, 36, $this->summaryColor($item['tone'] ?? 'primary'));
            $this->lineRect($x, $this->y - 42, $cardWidth, 36, 'D8E2EE');
            $this->textAt($item['label'], $x + 7, $this->y - 18, 7, false, '64748B');
            $this->textAt((string) $item['value'], $x + 7, $this->y - 33, 10, true, '172033');
            $x += $cardWidth + $gap;
        }

        $this->y -= 58;
        $this->textAt('Detalle del reporte', self::MARGIN, $this->y, 11, true, '172033');
        $this->y -= 12;
    }

    /**
     * Documentacion: Dibuja la tabla del PDF.
     * Como lo hace: Calcula anchos, envuelve texto, alterna fondos y crea nuevas paginas si falta espacio.
     */
    private function drawTable(array $columns, array $rows): void
    {
        if (empty($rows)) {
            $this->text('No hay registros para mostrar.', 10);
            return;
        }

        $widths = $this->calculateColumnWidths($columns);
        $this->drawTableHeader($columns, $widths);

        foreach ($rows as $index => $row) {
            $linesByColumn = [];
            $maxLines = 1;

            foreach ($columns as $columnIndex => $column) {
                $cellWidth = $widths[$columnIndex] - (self::CELL_PADDING * 2);
                $lines = $this->wrapForCell((string) ($row[$column] ?? ''), $cellWidth, self::TABLE_FONT_SIZE);
                $linesByColumn[] = $lines;
                $maxLines = max($maxLines, count($lines));
            }

            $rowHeight = max(18, min(44, ($maxLines * 8) + 8));

            if ($this->y - $rowHeight < self::MARGIN) {
                $this->finishPage();
                $this->startPage();
                $this->drawTableHeader($columns, $widths);
            }

            $fill = $index % 2 === 0 ? 'FFFFFF' : 'F8FBFF';
            $this->drawRow($columns, $widths, $linesByColumn, $rowHeight, $fill, false);
        }
    }

    /**
     * Documentacion: Dibuja encabezados de tabla.
     * Como lo hace: Envuelve titulos de columnas y pinta una fila destacada.
     */
    private function drawTableHeader(array $columns, array $widths): void
    {
        $linesByColumn = [];
        $maxLines = 1;

        foreach ($columns as $index => $column) {
            $lines = $this->wrapForCell($column, $widths[$index] - (self::CELL_PADDING * 2), self::HEADER_FONT_SIZE);
            $linesByColumn[] = $lines;
            $maxLines = max($maxLines, count($lines));
        }

        $height = max(20, ($maxLines * 8) + 8);
        $this->drawRow($columns, $widths, $linesByColumn, $height, 'DBEAFE', true);
    }

    /**
     * Documentacion: Dibuja una fila de tabla PDF.
     * Como lo hace: Pinta celdas, bordes y textos linea por linea respetando altura disponible.
     */
    private function drawRow(array $columns, array $widths, array $linesByColumn, float $height, string $fillColor, bool $bold): void
    {
        $x = self::MARGIN;
        $top = $this->y;
        $bottom = $top - $height;

        foreach ($columns as $index => $column) {
            $width = $widths[$index];
            $this->fillRect($x, $bottom, $width, $height, $fillColor);
            $this->lineRect($x, $bottom, $width, $height, 'CBD5E1');

            $lineY = $top - 10;
            foreach ($linesByColumn[$index] as $line) {
                if ($lineY < $bottom + 4) {
                    break;
                }

                $this->textAt($line, $x + self::CELL_PADDING, $lineY, $bold ? self::HEADER_FONT_SIZE : self::TABLE_FONT_SIZE, $bold, '1F2A3D');
                $lineY -= 8;
            }

            $x += $width;
        }

        $this->y -= $height;
    }

    /**
     * Documentacion: Calcula anchos proporcionales de columnas.
     * Como lo hace: Usa pesos por nombre de columna para repartir el ancho disponible.
     */
    private function calculateColumnWidths(array $columns): array
    {
        $weights = [
            'Historia' => 1.05,
            'Paciente' => 1.45,
            'Direccion' => 1.6,
            'Correo' => 1.35,
            'Telefono' => 1.05,
            'Fecha' => 1.0,
            'Hora' => .75,
            'Cita' => 1.15,
            'Tratamiento' => 1.45,
            'Profesional' => 1.3,
            'Motivo' => 1.45,
            'Productos' => 1.8,
            'Observaciones' => 1.65,
            'Referencia' => 1.05,
            'Registrado por' => 1.25,
            'Recibio' => 1.2,
            'Estado' => .9,
            'Metodo' => .9,
            'Total' => .85,
            'Pagado' => .85,
            'Saldo' => .85,
            'Monto' => .85,
            'Edad' => .55,
            'Sexo' => .8,
            'Citas' => .55,
            'Primera vez' => .85,
            'Pago' => .65,
        ];

        $totalWeight = array_sum(array_map(fn ($column) => $weights[$column] ?? 1, $columns));
        $availableWidth = self::PAGE_WIDTH - (self::MARGIN * 2);

        return array_map(
            fn ($column) => $availableWidth * (($weights[$column] ?? 1) / $totalWeight),
            $columns
        );
    }

    /**
     * Documentacion: Divide texto para que quepa en una celda.
     * Como lo hace: Limpia espacios, estima caracteres por ancho y limita cantidad de lineas.
     */
    private function wrapForCell(string $text, float $width, int $fontSize): array
    {
        $text = $this->limpiarTexto($text);
        $maxChars = max(6, (int) floor($width / ($fontSize * .48)));
        $lines = explode("\n", wordwrap($text, $maxChars, "\n", true));

        return array_slice($lines, 0, 5);
    }

    /**
     * Documentacion: Elige color de fondo para tarjetas del PDF.
     * Como lo hace: Mapea tonos semanticos a colores hexadecimales claros.
     */
    private function summaryColor(string $tone): string
    {
        return match ($tone) {
            'success' => 'EFFAF3',
            'danger' => 'FFF1F2',
            'warning' => 'FFF8E7',
            'info' => 'EDF9FA',
            default => 'EEF6FF',
        };
    }

    /**
     * Documentacion: Inicia una pagina PDF.
     * Como lo hace: Limpia comandos actuales y reinicia la coordenada vertical superior.
     */
    private function startPage(): void
    {
        $this->current = [];
        $this->y = self::PAGE_HEIGHT - self::MARGIN;
    }

    /**
     * Documentacion: Cierra la pagina PDF actual.
     * Como lo hace: Une los comandos generados y los agrega al arreglo de paginas.
     */
    private function finishPage(): void
    {
        $this->pages[] = implode("\n", $this->current);
    }

    /**
     * Documentacion: Asegura espacio vertical suficiente.
     * Como lo hace: Si no cabe el contenido, cierra pagina actual y abre una nueva.
     */
    private function ensureSpace(float $needed): void
    {
        if ($this->y - $needed >= self::MARGIN) {
            return;
        }

        $this->finishPage();
        $this->startPage();
    }

    /**
     * Documentacion: Escribe texto corrido en el PDF.
     * Como lo hace: Envuelve palabras, verifica espacio y posiciona cada linea.
     */
    private function text(string $text, int $size = 10, bool $bold = false): void
    {
        $maxChars = $this->maxChars($size);
        $lines = explode("\n", wordwrap($this->limpiarTexto($text), $maxChars, "\n", true));

        foreach ($lines as $line) {
            $this->ensureSpace($size + 6);
            $this->textAt($line, self::MARGIN, $this->y, $size, $bold);
            $this->y -= $size + 4;
        }
    }

    /**
     * Documentacion: Avanza espacio vertical en el PDF.
     * Como lo hace: Resta altura a la coordenada actual.
     */
    private function space(float $height): void
    {
        $this->y -= $height;
    }

    /**
     * Documentacion: Estima caracteres por linea.
     * Como lo hace: Ajusta el limite segun el tamano de fuente usado.
     */
    private function maxChars(int $size): int
    {
        return match (true) {
            $size >= 15 => 70,
            $size >= 12 => 90,
            $size >= 10 => 116,
            default => 150,
        };
    }

    /**
     * Documentacion: Pinta un rectangulo relleno en PDF.
     * Como lo hace: Convierte color hex a RGB y agrega el comando de relleno.
     */
    private function fillRect(float $x, float $y, float $width, float $height, string $hex): void
    {
        [$r, $g, $b] = $this->rgb($hex);
        $this->current[] = "{$r} {$g} {$b} rg {$x} {$y} {$width} {$height} re f";
    }

    /**
     * Documentacion: Dibuja el borde de un rectangulo en PDF.
     * Como lo hace: Convierte color hex a RGB y agrega el comando de trazo.
     */
    private function lineRect(float $x, float $y, float $width, float $height, string $hex): void
    {
        [$r, $g, $b] = $this->rgb($hex);
        $this->current[] = "{$r} {$g} {$b} RG 0.5 w {$x} {$y} {$width} {$height} re S";
    }

    /**
     * Documentacion: Escribe texto en una coordenada PDF.
     * Como lo hace: Escapa caracteres, elige fuente y agrega el comando de texto.
     */
    private function textAt(string $text, float $x, float $y, int $size, bool $bold = false, string $hex = '000000'): void
    {
        $font = $bold ? 'F2' : 'F1';
        $escaped = $this->escape($text);
        [$r, $g, $b] = $this->rgb($hex);

        $this->current[] = "{$r} {$g} {$b} rg BT /{$font} {$size} Tf {$x} {$y} Td ({$escaped}) Tj ET";
    }

    /**
     * Documentacion: Convierte color hexadecimal a RGB decimal.
     * Como lo hace: Divide el hex en canales y los normaliza entre 0 y 1.
     */
    private function rgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        return [
            round(hexdec(substr($hex, 0, 2)) / 255, 3),
            round(hexdec(substr($hex, 2, 2)) / 255, 3),
            round(hexdec(substr($hex, 4, 2)) / 255, 3),
        ];
    }

    /**
     * Documentacion: Normaliza texto antes de exportarlo.
     * Como lo hace: Reduce espacios repetidos y recorta extremos.
     */
    private function limpiarTexto(string $text): string
    {
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;
        return trim($text);
    }

    /**
     * Documentacion: Escapa texto para sintaxis PDF.
     * Como lo hace: Convierte a Windows-1252 y protege parentesis, saltos y barras.
     */
    private function escape(string $text): string
    {
        $encoded = mb_convert_encoding($text, 'Windows-1252', 'UTF-8');

        return str_replace(
            ['\\', '(', ')', "\r", "\n"],
            ['\\\\', '\\(', '\\)', ' ', ' '],
            $encoded
        );
    }

    /**
     * Documentacion: Construye el documento PDF final.
     * Como lo hace: Ensambla objetos, offsets, tabla xref, trailer y marca EOF.
     */
    private function buildPdf(): string
    {
        $objects = [];
        $pageRefs = [];

        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        $objects[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>';

        $nextId = 5;

        foreach ($this->pages as $pageContent) {
            $contentId = $nextId++;
            $pageId = $nextId++;
            $pageRefs[] = "{$pageId} 0 R";

            $stream = $pageContent . "\n";
            $objects[$contentId] = "<< /Length " . strlen($stream) . " >>\nstream\n{$stream}endstream";
            $objects[$pageId] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 ' . self::PAGE_WIDTH . ' ' . self::PAGE_HEIGHT . '] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents ' . $contentId . ' 0 R >>';
        }

        $objects[2] = '<< /Type /Pages /Kids [' . implode(' ', $pageRefs) . '] /Count ' . count($pageRefs) . ' >>';
        ksort($objects);

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $id => $object) {
            $offsets[$id] = strlen($pdf);
            $pdf .= "{$id} 0 obj\n{$object}\nendobj\n";
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xref}\n%%EOF";

        return $pdf;
    }
}
