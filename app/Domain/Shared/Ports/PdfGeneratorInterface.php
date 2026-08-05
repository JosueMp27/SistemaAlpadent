<?php

namespace App\Domain\Shared\Ports;

interface PdfGeneratorInterface
{
    public function streamPdf(string $html, string $filename = 'documento.pdf');
    public function downloadPdf(string $html, string $filename = 'documento.pdf');
}
