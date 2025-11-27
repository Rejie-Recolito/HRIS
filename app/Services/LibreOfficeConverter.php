<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class LibreOfficeConverter
{
    /**
     * Convert a DOCX to PDF using LibreOffice in headless mode.
     * Returns an array: [ 'exit' => int, 'stdout' => string, 'stderr' => string, 'pdf' => string|null ]
     */
    public function convertDocxToPdf(string $docxPath, string $outDir = null): array
    {
        $outDir = $outDir ?? storage_path('app/tmp');
        if (!is_dir($outDir)) {
            mkdir($outDir, 0775, true);
        }

        $binary = env('LIBREOFFICE_PATH') ?: '/usr/lib/libreoffice/program/soffice.bin';
        $basename = pathinfo($docxPath, PATHINFO_FILENAME);
        $pdfPath = rtrim($outDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $basename . '.pdf';

        // create per-conversion profile inside storage/tmp so the process user owns it when run from web
        $profileBase = storage_path('app/tmp/libreoffice_profile_' . uniqid());
        if (!is_dir($profileBase)) {
            mkdir($profileBase, 0700, true);
        }

        $userInstallation = $profileBase . '/LibreOffice_ConversionProfile';

        $cmd = [
            $binary,
            '-env:UserInstallation=file://' . $userInstallation,
            '--headless',
            '--convert-to',
            'pdf',
            '--outdir',
            $outDir,
            $docxPath,
        ];

        // Run process with HOME and XDG_RUNTIME_DIR pointing to the profile
        $process = new Process($cmd);
        $process->setTimeout(120);
        $process->setWorkingDirectory($outDir);
        $env = array_merge($_ENV, [
            'HOME' => $profileBase,
            'XDG_RUNTIME_DIR' => $profileBase,
        ]);
        $process->setEnv($env);

        try {
            $process->run();
        } catch (\Throwable $e) {
            Log::warning('LibreOffice convert exception', ['exception' => $e->getMessage(), 'cmd' => $cmd]);
            // cleanup profile
            $this->recursiveRmdir($profileBase);
            return ['exit' => 255, 'stdout' => '', 'stderr' => $e->getMessage(), 'pdf' => null];
        }

        $stdout = $process->getOutput();
        $stderr = $process->getErrorOutput();
        $exit = $process->getExitCode();

        Log::info('LibreOffice conversion finished', ['exit' => $exit, 'stdout' => $stdout, 'stderr' => $stderr]);

        // determine pdf path (some installs append exact name)
        $producedPdf = null;
        if (file_exists($pdfPath)) {
            $producedPdf = $pdfPath;
        } else {
            // try to find recently created pdf in outdir
            $files = glob(rtrim($outDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $basename . '*.pdf');
            if (!empty($files)) {
                $producedPdf = $files[0];
            }
        }

        // cleanup profile
        $this->recursiveRmdir($profileBase);

        return ['exit' => $exit, 'stdout' => $stdout, 'stderr' => $stderr, 'pdf' => $producedPdf];
    }

    private function recursiveRmdir(string $dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($items as $item) {
            if ($item->isDir()) {
                @rmdir($item->getRealPath());
            } else {
                @unlink($item->getRealPath());
            }
        }
        @rmdir($dir);
    }
}
