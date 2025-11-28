<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class LibreOfficeConverter
{
    /**
     * Convert a DOCX file to PDF using LibreOffice CLI.
     * Returns an array: ['exit' => int, 'stdout' => string, 'stderr' => string, 'pdf' => ?string, 'profile' => ?string]
     * On success pdf contains absolute path and profile is removed. On failure profile is preserved for inspection.
     *
     * @param string $docxPath absolute or relative path to DOCX
     * @param string|null $outDir absolute path to output dir (defaults to storage_path('app/tmp'))
     * @param int $timeout seconds for the process timeout
     * @return array
     */
    public static function convertDocxToPdf(string $docxPath, ?string $outDir = null, int $timeout = 120): array
    {
        $outDir = $outDir ?: storage_path('app/tmp');
        if (!is_dir($outDir)) {
            @mkdir($outDir, 0775, true);
        }

        $binary = self::findBinary();
        if (empty($binary)) {
            $msg = 'LibreOffice binary not found. Set LIBREOFFICE_PATH in .env or install LibreOffice.';
            Log::warning($msg);
            return ['exit' => 127, 'stdout' => '', 'stderr' => $msg, 'pdf' => null, 'profile' => null];
        }

        $profileDir = storage_path('app/tmp/libreoffice_profile_'.Str::random(12));
        @mkdir($profileDir, 0700, true);
        @mkdir($profileDir.'/runtime', 0700, true);

        $env = array_merge($_SERVER, [
            'HOME' => $profileDir,
            'XDG_RUNTIME_DIR' => $profileDir.'/runtime',
        ]);

        // Ensure absolute docx path
        $docxFull = $docxPath;
        if (!file_exists($docxFull)) {
            // try relative to project
            $docxFull = base_path($docxPath);
        }

        // Build a deterministic shell command (use the real soffice binary when available)
        $filter = 'pdf:writer_pdf_Export';
        $flags = '--headless --nologo --nodefault --norestore';
        $cmd = sprintf("%s %s -env:UserInstallation=file://%s --convert-to %s --outdir %s %s",
            escapeshellcmd($binary),
            $flags,
            escapeshellarg($profileDir),
            escapeshellarg($filter),
            escapeshellarg($outDir),
            escapeshellarg($docxFull)
        );

        Log::info('Running LibreOffice command', ['cmd' => $cmd]);

        // Run process via shell so quoting matches manual runs
        $process = Process::fromShellCommandline($cmd);
        $process->setTimeout($timeout);
        try {
            $process->setWorkingDirectory($outDir);
            $process->setEnv($env);
            $process->run();
        } catch (\Throwable $e) {
            $stderr = $e->getMessage();
            // persist error files
            @file_put_contents($profileDir.'/soffice_out.txt', '');
            @file_put_contents($profileDir.'/soffice_err.txt', $stderr);
            Log::warning('LibreOffice conversion exception: '.$stderr);
            return ['exit' => 1, 'stdout' => '', 'stderr' => $stderr, 'pdf' => null, 'profile' => $profileDir];
        }

        $stdout = $process->getOutput();
        $stderr = $process->getErrorOutput();

        // persist outputs for debugging
        @file_put_contents($profileDir.'/soffice_out.txt', $stdout);
        @file_put_contents($profileDir.'/soffice_err.txt', $stderr);

        // also collect any internal LibreOffice log files from the profile and append to soffice_err.txt
        try {
            $logs = @glob($profileDir.'/LibreOffice_ConversionProfile/**/**/*.log', GLOB_BRACE) ?: [];
            // fallback: search any .log files (depth-limited)
            if (empty($logs)) {
                $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($profileDir));
                foreach ($it as $f) {
                    if ($f->isFile() && preg_match('/\.log$/i', $f->getFilename())) {
                        $logs[] = $f->getRealPath();
                    }
                }
            }
            foreach ($logs as $l) {
                $content = @file_get_contents($l);
                if ($content !== false && trim($content) !== '') {
                    @file_put_contents($profileDir.'/soffice_err.txt', "\n---- INTERNAL LOG: $l ----\n".substr($content,0,20000), FILE_APPEND);
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $exit = $process->getExitCode() ?? 1;

        // find produced PDF
        $pdfCandidate = null;
        $base = pathinfo($docxFull, PATHINFO_FILENAME);
        $files = @scandir($outDir) ?: [];
        foreach ($files as $f) {
            if (stripos($f, $base) !== false && Str::endsWith($f, '.pdf')) {
                $pdfCandidate = rtrim($outDir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$f;
                break;
            }
        }

        if ($exit === 0 && $pdfCandidate && file_exists($pdfCandidate)) {
            // success: remove profile dir
            try {
                self::deleteDirectory($profileDir);
            } catch (\Throwable $e) {
                // best effort
                Log::info('Could not delete libreoffice profile dir: '.$profileDir.' '.$e->getMessage());
            }
            return ['exit' => 0, 'stdout' => $stdout, 'stderr' => $stderr, 'pdf' => $pdfCandidate, 'profile' => null];
        }

        // failure: attempt fallback converter (PhpWord -> HTML -> Dompdf)
        Log::warning('LibreOffice conversion failed, attempting fallback', ['exit' => $exit, 'stdout' => substr($stdout,0,2000), 'stderr' => substr($stderr,0,2000), 'profile' => $profileDir]);
        try {
            $fallbackPdf = \App\Services\DocxToPdfFallback::convert($docxFull, $outDir);
            if ($fallbackPdf) {
                // success via fallback: remove profile
                try { self::deleteDirectory($profileDir); } catch (\Throwable $e) { /* best effort */ }
                return ['exit' => 0, 'stdout' => $stdout, 'stderr' => $stderr, 'pdf' => $fallbackPdf, 'profile' => null, 'fallback' => true];
            }
        } catch (\Throwable $e) {
            Log::warning('Fallback converter threw: '.$e->getMessage());
        }

        // keep profile for debugging
        Log::warning('LibreOffice conversion failed (fallback also failed)', ['exit' => $exit, 'stdout' => substr($stdout,0,2000), 'stderr' => substr($stderr,0,2000), 'profile' => $profileDir]);
        return ['exit' => $exit, 'stdout' => $stdout, 'stderr' => $stderr, 'pdf' => $pdfCandidate, 'profile' => $profileDir];
    }

    private static function findBinary(): ?string
    {
        $candidates = [];
        $envPath = env('LIBREOFFICE_PATH');
        if ($envPath) {
            $candidates[] = $envPath;
        }
        // common linux locations
        $candidates = array_merge($candidates, [
            '/usr/lib/libreoffice/program/soffice.bin',
            '/usr/lib/libreoffice/program/soffice',
            '/usr/bin/soffice',
            'soffice',
        ]);

        foreach ($candidates as $c) {
            if (!$c) continue;
            // if absolute path and executable
            if (strpos($c, '/') === 0 && is_executable($c)) {
                return $c;
            }
            // try which for commands
            $which = trim(shell_exec('which '.escapeshellarg($c).' 2>/dev/null'));
            if ($which) {
                return $which;
            }
        }

        return null;
    }

    private static function deleteDirectory(string $dir): void
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
