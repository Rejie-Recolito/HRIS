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

        // Prefer explicit env path; fall back to common locations, prefer soffice.bin when available
        $candidates = [];
        if ($envBin = env('LIBREOFFICE_PATH')) {
            $candidates[] = $envBin;
        }
        $candidates = array_merge($candidates, [
            '/usr/lib/libreoffice/program/soffice.bin',
            '/usr/lib/libreoffice/program/soffice',
            '/usr/bin/soffice',
            '/usr/local/bin/soffice',
            '/snap/bin/soffice',
            'soffice',
        ]);
        $binary = null;
        foreach ($candidates as $c) {
            if ($c === 'soffice') {
                // rely on PATH
                $which = null;
                @exec('which soffice 2>/dev/null', $whichOut, $whichRc);
                if (!empty($whichOut) && file_exists(trim($whichOut[0]))) {
                    $binary = trim($whichOut[0]);
                    break;
                }
                continue;
            }
            if (file_exists($c) && is_executable($c)) {
                $binary = $c;
                break;
            }
        }
        if (!$binary) {
            // fallback to env even if not executable (best effort)
            $binary = env('LIBREOFFICE_PATH') ?: '/usr/lib/libreoffice/program/soffice.bin';
        }
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
            $stdout = '';
            $stderr = $e->getMessage();
            $exit = 255;
            Log::warning('LibreOffice convert exception', ['exception' => $stderr, 'cmd' => $cmd]);

            // persist error output for inspection
            @file_put_contents($profileBase . '/soffice_out.txt', $stdout);
            @file_put_contents($profileBase . '/soffice_err.txt', $stderr);

            return ['exit' => $exit, 'stdout' => $stdout, 'stderr' => $stderr, 'pdf' => null, 'profile' => $profileBase];
        }

        $stdout = $process->getOutput();
        $stderr = $process->getErrorOutput();
        $exit = $process->getExitCode();

        // persist outputs for debugging in profile
        @file_put_contents($profileBase . '/soffice_out.txt', $stdout);
        @file_put_contents($profileBase . '/soffice_err.txt', $stderr);

        Log::info('LibreOffice conversion finished', ['exit' => $exit, 'stdout' => $stdout, 'stderr' => $stderr, 'profile' => $profileBase]);

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

        // cleanup profile only on success
        if ($exit === 0) {
            $this->recursiveRmdir($profileBase);
            return ['exit' => $exit, 'stdout' => $stdout, 'stderr' => $stderr, 'pdf' => $producedPdf];
        }

        // keep profile for debugging and return its path
        return ['exit' => $exit, 'stdout' => $stdout, 'stderr' => $stderr, 'pdf' => $producedPdf, 'profile' => $profileBase];
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
