<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

class DocxToPdfFallback
{
    /**
     * Convert a DOCX to PDF via PhpWord -> HTML -> Dompdf
     * Returns absolute PDF path on success, or null on failure.
     */
    public static function convert(string $docxPath, string $outDir): ?string
    {
        if (!file_exists($docxPath)) {
            Log::warning('DocxToPdfFallback: input docx not found', ['docx' => $docxPath]);
            return null;
        }

        try {
            // Load DOCX into PhpWord
            $phpWord = IOFactory::load($docxPath, 'Word2007');

            // Save as HTML to a temporary file
            $tmpHtml = tempnam(sys_get_temp_dir(), 'docx_html_') . '.html';
            $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
            $htmlWriter->save($tmpHtml);

            // Render HTML to PDF using Dompdf (via barryvdh/laravel-dompdf facade if available)
            $pdfName = pathinfo($docxPath, PATHINFO_FILENAME) . '.pdf';
            $outPath = rtrim($outDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $pdfName;

            // If the Laravel PDF facade exists, use it; otherwise try Dompdf directly
            if (class_exists(Pdf::class)) {
                $html = file_get_contents($tmpHtml);
                $pdf = Pdf::loadHTML($html)->setPaper('legal', 'portrait');
                $pdf->save($outPath);
            } else {
                // Attempt to use Dompdf directly
                if (!class_exists('\Dompdf\Dompdf')) {
                    Log::warning('DocxToPdfFallback: Dompdf not available');
                    @unlink($tmpHtml);
                    return null;
                }
                $dompdf = new \Dompdf\Dompdf();
                $dompdf->loadHtml(file_get_contents($tmpHtml));
                $dompdf->setPaper('legal', 'portrait');
                $dompdf->render();
                file_put_contents($outPath, $dompdf->output());
            }

            @unlink($tmpHtml);
            if (file_exists($outPath) && filesize($outPath) > 0) {
                return $outPath;
            }
            return null;
        } catch (\Throwable $e) {
            Log::warning('DocxToPdfFallback failed: '.$e->getMessage());
            return null;
        }
    }
}
