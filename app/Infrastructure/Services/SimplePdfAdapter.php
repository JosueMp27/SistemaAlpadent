<?php

namespace App\Infrastructure\Services;

use App\Domain\Shared\Ports\PdfGeneratorInterface;
use App\Services\SimplePdfService;

class SimplePdfAdapter implements PdfGeneratorInterface
{
    private SimplePdfService $pdfService;

    public function __construct(?SimplePdfService $pdfService = null)
    {
        $this->pdfService = $pdfService ?? new SimplePdfService();
    }

    public function streamPdf(string $html, string $filename = 'documento.pdf')
    {
        // Wrapper for stream if needed or raw binary PDF response
        return response($html, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function downloadPdf(string $html, string $filename = 'documento.pdf')
    {
        return response($html, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
