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

        // Create the LibreOffice user profile under system temp (like LeaveApplicationController)
        $profileDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'libreoffice_profile_' . uniqid();
        if (!is_dir($profileDir)) {
            @mkdir($profileDir, 0700, true);
        }
        @mkdir($profileDir . '/runtime', 0700, true);

        // Ensure absolute docx path
        $docxFull = $docxPath;
        if (!file_exists($docxFull)) {
            // try relative to project
            $docxFull = base_path($docxPath);
        }


        // Build command using the same exec-based pattern as LeaveApplicationController
        // Use an env prefix so HOME and XDG_RUNTIME_DIR are set for the soffice process
        $loUserInstallation = 'file://' . str_replace('\\', '/', $profileDir);
        $envPrefix = 'HOME=' . escapeshellarg($profileDir) . ' XDG_RUNTIME_DIR=' . escapeshellarg($profileDir . '/runtime') . ' ';

        $cmd = $envPrefix . escapeshellcmd($binary)
            . ' --headless -env:UserInstallation=' . escapeshellarg($loUserInstallation)
            . ' --convert-to pdf --outdir ' . escapeshellarg($outDir) . ' ' . escapeshellarg($docxFull)
            . ' 2>&1';

        Log::info('Running LibreOffice exec command', ['cmd' => $cmd]);

        // exec captures combined stdout+stderr into $output array and $exitCode
        $outputLines = [];
        $exitCode = 1;
        @exec($cmd, $outputLines, $exitCode);
        $stdout = implode("\n", $outputLines);
        $stderr = ''; // exec merged stderr into $outputLines; internal logs will be collected below

        // persist outputs for debugging (exec merges stderr into stdout)
        @file_put_contents($profileDir.'/soffice_out.txt', $stdout);
        if (!file_exists($profileDir.'/soffice_err.txt')) {
            @file_put_contents($profileDir.'/soffice_err.txt', "");
        }

        

        // also collect any internal LibreOffice log-like files from the profile and append to soffice_err.txt
        try {
            $collected = 0;
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($profileDir, \RecursiveDirectoryIterator::SKIP_DOTS));
            foreach ($it as $f) {
                if (!$f->isFile()) continue;
                $name = $f->getFilename();
                // match a variety of likely log-like files produced by LibreOffice
                if (!preg_match('/\.(log|out|err|trace|txt)$/i', $name) && stripos($name, 'stderr') === false && stripos($name, 'stdout') === false) {
                    continue;
                }
                $path = $f->getRealPath();
                $content = @file_get_contents($path);
                if ($content === false) continue;
                $content = trim($content);
                // write a header and the content (truncate large files)
                $header = "\n---- INTERNAL LOG: {$path} (size=".filesize($path)." bytes) ----\n";
                if ($content === '') {
                    @file_put_contents($profileDir.'/soffice_err.txt', $header."[empty]\n", FILE_APPEND);
                } else {
                    // keep at most 200KB per internal file to avoid huge outputs
                    $snippet = (strlen($content) > 200*1024) ? substr($content, -200*1024) : $content;
                    @file_put_contents($profileDir.'/soffice_err.txt', $header.$snippet.PHP_EOL, FILE_APPEND);
                }
                $collected++;
            }
            if ($collected === 0) {
                @file_put_contents($profileDir.'/soffice_err.txt', "\n---- NO INTERNAL LOG FILES FOUND UNDER PROFILE (scanned path={$profileDir}) ----\n", FILE_APPEND);
            }
        } catch (\Throwable $e) {
            // ensure at least an explanatory message is present
            @file_put_contents($profileDir.'/soffice_err.txt', "\n---- LOG COLLECTION FAILED: ".substr($e->getMessage(),0,100)." ----\n", FILE_APPEND);
        }

    $exit = $exitCode ?? 1;

        Log::info('LibreOffice exec finished', ['exit' => $exit, 'lines' => min(50, count($outputLines))]);

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

        // failure: preserve profile for debugging and return failure result
        Log::warning('LibreOffice conversion failed', ['exit' => $exit, 'stdout' => substr($stdout,0,2000), 'stderr' => substr($stderr,0,2000), 'profile' => $profileDir]);
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
