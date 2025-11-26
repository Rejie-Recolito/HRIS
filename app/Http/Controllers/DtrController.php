<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DtrCsvParser;
use App\Models\DtrUpload;
use App\Jobs\ProcessDtrUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Tab;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\TemplateProcessor;


class DtrController {

    // Admin DTR search by emp_id and month

    // Employee self DTR view by month

    // Admin DTR search by emp_id and month
    public function adminSearch(Request $request)
    {
        $request->validate([
            'emp_id' => 'required|string',
            'month' => 'required|date_format:Y-m',
        ]);


        $empId = trim($request->input('emp_id'));
        $month = $request->input('month');

        // Debug: collect available employee IDs from existing DTR entries
        // (the employees table in this project does not currently have an `employee_id` column,
        //  so use the DTR entries as the source of truth for searchable IDs)
        $allEmpIds = \App\Models\DtrEntry::whereNotNull('emp_id')
            ->distinct()
            ->pluck('emp_id')
            ->filter()
            ->values()
            ->all();

    Log::info('Searching for emp_id: ' . $empId);
    Log::info('Available employee_ids: ' . implode(',', $allEmpIds));

        // Validate that the provided emp_id exists in the DTR entries
        if (!in_array($empId, $allEmpIds)) {
            return back()->withErrors(['emp_id' => 'Employee ID not found. (Available: ' . implode(', ', $allEmpIds) . ')'])->withInput();
        }

        // Get DTR entries for this emp_id and month
        $dtrEntries = \App\Models\DtrEntry::where('emp_id', $empId)
            ->where('occurred_at', 'like', $month . '%')
            ->orderBy('occurred_at')
            ->get();

        // Fetch uploads for sidebar/list
        $uploads = \App\Models\DtrUpload::orderByDesc('created_at')->limit(10)->get();

        return view('admin.dtr', [
            'uploads' => $uploads,
            'dtrEntries' => $dtrEntries,
        ]);
    }

    /**
     * Generate a PDF version of the admin DTR search results.
     * Expects query parameters: emp_id and month (Y-m)
     */
    public function generatePdf(Request $request)
    {
        $empId = trim($request->input('emp_id'));
        $month = $request->input('month');

        if (!$empId || !$month) {
            return back()->withErrors(['pdf' => 'Missing emp_id or month for PDF generation.']);
        }

        $dtrEntries = \App\Models\DtrEntry::where('emp_id', $empId)
            ->where('occurred_at', 'like', $month . '%')
            ->orderBy('occurred_at')
            ->get();

        $pdf = Pdf::loadView('admin.dtr_pdf', [
            'dtrEntries' => $dtrEntries,
            'empId' => $empId,
            'month' => $month,
        ]);

        $filename = "dtr_{$empId}_{$month}.pdf";
        return $pdf->download($filename);
    }

    /**
     * Generate DOCX based DTR and convert to PDF using LibreOffice (soffice).
     * This programmatically creates a DOCX that matches the paper template (days 1..31)
     * and fills times from DtrEntry.occurred_at for the selected month.
     */
    public function generateDocxPdf(Request $request)
    {
        $empId = trim($request->input('emp_id'));
        $month = $request->input('month'); // format Y-m

        if (!$empId || !$month) {
            return back()->withErrors(['pdf' => 'Missing emp_id or month for PDF generation.']);
        }

        // Parse month and build date range
        try {
            $start = \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Exception $e) {
            return back()->withErrors(['month' => 'Invalid month format.']);
        }

        $daysInMonth = $start->daysInMonth;

        // Fetch entries for the month keyed by day
        $entries = \App\Models\DtrEntry::where('emp_id', $empId)
            ->where('occurred_at', 'like', $month . '%')
            ->orderBy('occurred_at')
            ->get()
            ->groupBy(function ($e) {
                return \Carbon\Carbon::parse($e->occurred_at)->day;
            });

        // If a DOCX template is provided, use TemplateProcessor to populate it (preferred)
        $templatePath = resource_path('templates/dtr_template.docx');
        $tmpDir = storage_path('app/tmp');
        if (!file_exists($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        $base = 'dtr_' . preg_replace('/[^A-Za-z0-9-_]/', '_', $empId) . '_' . $month;
        $docxPath = $tmpDir . DIRECTORY_SEPARATOR . $base . '.docx';
        $pdfPath = $tmpDir . DIRECTORY_SEPARATOR . $base . '.pdf';

        if (file_exists($templatePath)) {
            // Template must contain placeholders: EMP_ID, MONTH and a table row with placeholders
            // day, am_arrival, am_departure, pm_arrival, pm_departure, undertime_hours, undertime_minutes
            $tpl = new TemplateProcessor($templatePath);
            $tpl->setValue('EMP_ID', $empId);
            $tpl->setValue('MONTH', $start->format('F Y'));

            // clone the row placeholder 'day' daysInMonth times
            $tpl->cloneRow('day', $daysInMonth);

            Log::info('generateDocxPdf: grouped entries summary', ['emp' => $empId, 'month' => $month, 'groups' => array_keys($entries->toArray())]);
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $dayEntries = $entries->get($i, collect());
                $amArrival = '';
                $amDeparture = '';
                $pmArrival = '';
                $pmDeparture = '';

                foreach ($dayEntries as $ent) {
                    // If the parser stored day-level AM/PM fields in raw, prefer them.
                    $raw = $ent->raw ?? null;
                    if (is_string($raw)) {
                        $tmp = @json_decode($raw, true);
                        if (is_array($tmp)) $raw = $tmp;
                    }
                    if (is_array($raw)) {
                        $keys = function ($variants) use ($raw) {
                            foreach ($variants as $k) {
                                if (array_key_exists($k, $raw) && $raw[$k] !== '') return $raw[$k];
                            }
                            // case-insensitive lookup
                            foreach ($raw as $rk => $rv) {
                                $lk = strtolower($rk);
                                foreach ($variants as $k) {
                                    if ($lk === strtolower($k) && $rv !== '') return $rv;
                                }
                            }
                            return null;
                        };

                        $am_a = $keys(['AM-Arrival', 'AM Arrival', 'AM_Arrival', 'am_arrival', 'am-arrival', 'AMArrival']);
                        $am_d = $keys(['AM-Departure', 'AM Departure', 'AM_Departure', 'am_departure', 'am-departure', 'AMDeparture']);
                        $pm_a = $keys(['PM-Arrival', 'PM Arrival', 'PM_Arrival', 'pm_arrival', 'pm-arrival', 'PMArrival']);
                        $pm_d = $keys(['PM-Departure', 'PM Departure', 'PM_Departure', 'pm_departure', 'pm-departure', 'PMDeparture']);

                        if ($am_a && !$amArrival) $amArrival = $am_a;
                        if ($am_d && !$amDeparture) $amDeparture = $am_d;
                        if ($pm_a && !$pmArrival) $pmArrival = $pm_a;
                        if ($pm_d && !$pmDeparture) $pmDeparture = $pm_d;

                        // If raw provided explicit values, skip the later heuristics for this entry
                        if ($am_a || $am_d || $pm_a || $pm_d) {
                            continue;
                        }
                    }
                    // Determine arrival hour from time_in when present, otherwise from occurred_at
                    $arrHour = null;
                    $depHour = null;
                    $occur = \Carbon\Carbon::parse($ent->occurred_at);
                    if (!empty($ent->time_in)) {
                        try {
                            $arrHour = (int)\Carbon\Carbon::parse($ent->time_in)->format('H');
                        } catch (\Exception $e) {
                            // ignore parse error
                        }
                    }
                    if (!empty($ent->time_out)) {
                        try {
                            $depHour = (int)\Carbon\Carbon::parse($ent->time_out)->format('H');
                        } catch (\Exception $e) {
                            // ignore parse error
                        }
                    }
                    // fallback to occurred_at for each if specific times not present
                    if ($arrHour === null) {
                        $arrHour = (int)$occur->format('H');
                    }
                    if ($depHour === null) {
                        $depHour = (int)$occur->format('H');
                    }

                    // Assign arrival
                    if ($arrHour < 12) {
                        if (!$amArrival) {
                            $amArrival = !empty($ent->time_in) ? $ent->time_in : $occur->format('H:i');
                        }
                    } else {
                        if (!$pmArrival) {
                            $pmArrival = !empty($ent->time_in) ? $ent->time_in : $occur->format('H:i');
                        }
                    }

                    // Assign departure
                    if ($depHour < 12) {
                        if (!$amDeparture) {
                            $amDeparture = !empty($ent->time_out) ? $ent->time_out : $occur->format('H:i');
                        }
                    } else {
                        if (!$pmDeparture) {
                            $pmDeparture = !empty($ent->time_out) ? $ent->time_out : $occur->format('H:i');
                        }
                    }
                }

                $tpl->setValue("day#{$i}", $i);
                $tpl->setValue("am_arrival#{$i}", $amArrival);
                $tpl->setValue("am_departure#{$i}", $amDeparture);
                $tpl->setValue("pm_arrival#{$i}", $pmArrival);
                $tpl->setValue("pm_departure#{$i}", $pmDeparture);
                $tpl->setValue("undertime_hours#{$i}", '');
                $tpl->setValue("undertime_minutes#{$i}", '');
            }

            $tpl->saveAs($docxPath);

            // Convert DOCX -> PDF via soffice (auto-detect path)
            // Auto-detect soffice binary: try PATH then common install locations
            $found = null;
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $whereOut = [];
                @exec('where soffice', $whereOut, $whereRc);
                if (!empty($whereOut)) {
                    $found = trim($whereOut[0]);
                }
                $candidates = [
                    'C:\\Program Files\\LibreOffice\\program\\soffice.exe',
                    'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe'
                ];
                foreach ($candidates as $c) {
                    if ($found) break;
                    if (file_exists($c)) $found = $c;
                }
            } else {
                $whichOut = [];
                @exec('which soffice', $whichOut, $whichRc);
                if (!empty($whichOut)) {
                    $found = trim($whichOut[0]);
                }
                // Common linux locations
                $candidates = ['/usr/bin/soffice', '/usr/local/bin/soffice', '/snap/bin/soffice'];
                foreach ($candidates as $c) {
                    if ($found) break;
                    if (file_exists($c)) $found = $c;
                }
            }

            if (!$found) {
                return back()->withErrors(['pdf' => "Failed to convert DOCX to PDF. LibreOffice 'soffice' binary not found. Install LibreOffice or set 'services.libreoffice.path' in config/services.php. Detected OS: " . PHP_OS]);
            }

            // Quote the soffice executable correctly. On Windows use double quotes, on Unix escapeshellcmd is fine.
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $foundQuoted = '"' . str_replace('"', '\\"', $found) . '"';
            } else {
                $foundQuoted = escapeshellcmd($found);
            }
            $cmd = $foundQuoted . ' --headless --convert-to pdf --outdir ' . escapeshellarg($tmpDir) . ' ' . escapeshellarg($docxPath) . ' 2>&1';
            // Allow more time for the conversion to complete
            if (function_exists('set_time_limit')) {
                @set_time_limit(120);
            }
            $output = [];
            $rc = 0;
            @exec($cmd, $output, $rc);
            if (!file_exists($pdfPath)) {
                $outStr = trim(implode("\n", array_slice($output, 0, 200)));
                Log::error('soffice conversion failed', ['cmd' => $cmd, 'rc' => $rc, 'output' => implode("\n", $output), 'docx' => $docxPath, 'pdf' => $pdfPath]);
                return back()->withErrors(['pdf' => 'Failed to convert DOCX to PDF. soffice exit code: ' . $rc . '. Output (truncated): ' . substr($outStr, 0, 1000) . '. Check storage/logs/laravel.log for full output. Also verify soffice is installed and that PHP can execute external commands and write to storage/app/tmp.']);
            }

            return response()->download($pdfPath)->deleteFileAfterSend(true);
        }

        // Fallback: programmatic generation if no template exists
        $phpWord = new PhpWord();
        $section = $phpWord->addSection(['marginTop' => 600, 'marginBottom' => 600]);

        // Header
        $section->addText('DAILY TIME RECORD', ['bold' => true, 'size' => 12], ['alignment' => 'center']);
        $section->addTextBreak(1);

        // Basic info
        $section->addText('Employee ID: ' . $empId);
        $section->addText('Month: ' . $start->format('F Y'));
        $section->addTextBreak(1);

        // Table with columns (Day, AM Arrival, AM Departure, PM Arrival, PM Departure, Undertime Hours, Minutes)
        $tableStyleName = 'DTRTable';
        $phpWord->addTableStyle($tableStyleName, [
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 50],
            ['exactWidth' => true]
        );

        $table = $section->addTable($tableStyleName);
        $table->addRow();
        $table->addCell(Converter::cmToTwip(1))->addText('Day');
        $table->addCell(Converter::cmToTwip(2))->addText('AM Arrival');
        $table->addCell(Converter::cmToTwip(2))->addText('AM Departure');
        $table->addCell(Converter::cmToTwip(2))->addText('PM Arrival');
        $table->addCell(Converter::cmToTwip(2))->addText('PM Departure');
        $table->addCell(Converter::cmToTwip(2))->addText('Undertime Hours');
        $table->addCell(Converter::cmToTwip(2))->addText('Undertime Minutes');

        Log::info('generateDocxPdf fallback: grouped entries summary', ['emp' => $empId, 'month' => $month, 'groups' => array_keys($entries->toArray())]);
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $table->addRow();
            $table->addCell(Converter::cmToTwip(1))->addText((string)$d);

            $dayEntries = $entries->get($d, collect());
            // Simple logic: take first AM and first PM entries per day if present
            $amArrival = '';
            $amDeparture = '';
            $pmArrival = '';
            $pmDeparture = '';

            foreach ($dayEntries as $ent) {
                // If the parser stored day-level AM/PM fields in raw, prefer them.
                $raw = $ent->raw ?? null;
                if (is_string($raw)) {
                    $tmp = @json_decode($raw, true);
                    if (is_array($tmp)) $raw = $tmp;
                }
                if (is_array($raw)) {
                    $keys = function ($variants) use ($raw) {
                        foreach ($variants as $k) {
                            if (array_key_exists($k, $raw) && $raw[$k] !== '') return $raw[$k];
                        }
                        foreach ($raw as $rk => $rv) {
                            $lk = strtolower($rk);
                            foreach ($variants as $k) {
                                if ($lk === strtolower($k) && $rv !== '') return $rv;
                            }
                        }
                        return null;
                    };

                    $am_a = $keys(['AM-Arrival', 'AM Arrival', 'AM_Arrival', 'am_arrival', 'am-arrival', 'AMArrival']);
                    $am_d = $keys(['AM-Departure', 'AM Departure', 'AM_Departure', 'am_departure', 'am-departure', 'AMDeparture']);
                    $pm_a = $keys(['PM-Arrival', 'PM Arrival', 'PM_Arrival', 'pm_arrival', 'pm-arrival', 'PMArrival']);
                    $pm_d = $keys(['PM-Departure', 'PM Departure', 'PM_Departure', 'pm_departure', 'pm-departure', 'PMDeparture']);

                    if ($am_a && !$amArrival) $amArrival = $am_a;
                    if ($am_d && !$amDeparture) $amDeparture = $am_d;
                    if ($pm_a && !$pmArrival) $pmArrival = $pm_a;
                    if ($pm_d && !$pmDeparture) $pmDeparture = $pm_d;

                    if ($am_a || $am_d || $pm_a || $pm_d) {
                        continue;
                    }
                }
                // Determine arrival and departure hours from explicit fields when present
                $occur = \Carbon\Carbon::parse($ent->occurred_at);
                $arrHour = null;
                $depHour = null;
                if (!empty($ent->time_in)) {
                    try { $arrHour = (int)\Carbon\Carbon::parse($ent->time_in)->format('H'); } catch (\Exception $e) {}
                }
                if (!empty($ent->time_out)) {
                    try { $depHour = (int)\Carbon\Carbon::parse($ent->time_out)->format('H'); } catch (\Exception $e) {}
                }
                if ($arrHour === null) $arrHour = (int)$occur->format('H');
                if ($depHour === null) $depHour = (int)$occur->format('H');

                if ($arrHour < 12) {
                    if (!$amArrival) {
                        $amArrival = !empty($ent->time_in) ? $ent->time_in : $occur->format('H:i');
                    }
                } else {
                    if (!$pmArrival) {
                        $pmArrival = !empty($ent->time_in) ? $ent->time_in : $occur->format('H:i');
                    }
                }

                if ($depHour < 12) {
                    if (!$amDeparture) {
                        $amDeparture = !empty($ent->time_out) ? $ent->time_out : $occur->format('H:i');
                    }
                } else {
                    if (!$pmDeparture) {
                        $pmDeparture = !empty($ent->time_out) ? $ent->time_out : $occur->format('H:i');
                    }
                }
            }

            $table->addCell(Converter::cmToTwip(2))->addText($amArrival);
            $table->addCell(Converter::cmToTwip(2))->addText($amDeparture);
            $table->addCell(Converter::cmToTwip(2))->addText($pmArrival);
            $table->addCell(Converter::cmToTwip(2))->addText($pmDeparture);
            $table->addCell(Converter::cmToTwip(2))->addText('');
            $table->addCell(Converter::cmToTwip(2))->addText('');
        }

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($docxPath);

        // Convert DOCX -> PDF via soffice (auto-detect path)
        // Auto-detect soffice binary: try PATH then common install locations
        $found = null;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $whereOut = [];
            @exec('where soffice', $whereOut, $whereRc);
            if (!empty($whereOut)) {
                $found = trim($whereOut[0]);
            }
            $candidates = [
                'C:\\Program Files\\LibreOffice\\program\\soffice.exe',
                'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe'
            ];
            foreach ($candidates as $c) {
                if ($found) break;
                if (file_exists($c)) $found = $c;
            }
        } else {
            $whichOut = [];
            @exec('which soffice', $whichOut, $whichRc);
            if (!empty($whichOut)) {
                $found = trim($whichOut[0]);
            }
            // Common linux locations
            $candidates = ['/usr/bin/soffice', '/usr/local/bin/soffice', '/snap/bin/soffice'];
            foreach ($candidates as $c) {
                if ($found) break;
                if (file_exists($c)) $found = $c;
            }
        }

        if (!$found) {
            return back()->withErrors(['pdf' => "Failed to convert DOCX to PDF. LibreOffice 'soffice' binary not found. Install LibreOffice or set 'services.libreoffice.path' in config/services.php. Detected OS: " . PHP_OS]);
        }

        // Quote the soffice executable correctly for Windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $foundQuoted = '"' . str_replace('"', '\\"', $found) . '"';
        } else {
            $foundQuoted = escapeshellcmd($found);
        }
        $cmd = $foundQuoted . ' --headless --convert-to pdf --outdir ' . escapeshellarg($tmpDir) . ' ' . escapeshellarg($docxPath) . ' 2>&1';
        if (function_exists('set_time_limit')) {
            @set_time_limit(120);
        }
        $output = [];
        $rc = 0;
        @exec($cmd, $output, $rc);
        if (!file_exists($pdfPath)) {
            $outStr = trim(implode("\n", array_slice($output, 0, 200)));
            Log::error('soffice conversion failed (fallback)', ['cmd' => $cmd, 'rc' => $rc, 'output' => implode("\n", $output), 'docx' => $docxPath, 'pdf' => $pdfPath]);
            return back()->withErrors(['pdf' => 'Failed to convert DOCX to PDF (fallback). soffice exit code: ' . $rc . '. Output (truncated): ' . substr($outStr, 0, 1000) . '. Check storage/logs/laravel.log for full output and verify permissions.']);
        }

        return response()->download($pdfPath)->deleteFileAfterSend(true);
    }

    // Employee self DTR view by month
    public function employeeSelfView(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee ?? null;
        if (!$employee) {
            return back()->withErrors(['employee' => 'Employee profile not found.']);
        }

        $month = $request->input('month', now()->format('Y-m'));

        $dtrEntries = \App\Models\DtrEntry::where('emp_id', $employee->emp_id)
            ->where('occurred_at', 'like', $month . '%')
            ->orderBy('occurred_at')
            ->get();

        $uploads = \App\Models\DtrUpload::orderByDesc('created_at')->limit(10)->get();

        return view('admin.dtr', [
            'uploads' => $uploads,
            'dtrEntries' => $dtrEntries,
        ]);
    }
    // List all DTR uploads for the admin view
    public function uploadsList()
    {
        $uploads = \App\Models\DtrUpload::orderByDesc('created_at')->get();
        return view('admin.dtr_uploads', ['uploads' => $uploads]);
    }

    // View a single DTR upload and its entries
    public function viewUpload($uploadId)
    {
        $upload = \App\Models\DtrUpload::findOrFail($uploadId);
        $entries = $upload->entries()->orderBy('occurred_at')->get();
        return view('admin.dtr_view', [
            'upload' => $upload,
            'entries' => $entries,
        ]);
    }

    // Delete a DTR upload and its entries
    public function deleteUpload($uploadId)
    {
        $upload = DtrUpload::findOrFail($uploadId);
        $upload->entries()->delete();
        $upload->delete();
        return redirect()->route('admin.dtr.uploads')->with('success', 'DTR upload deleted successfully.');
    }
    protected $parser;

    public function __construct(DtrCsvParser $parser)
    {
        $this->parser = $parser;
    }

    public function show()
    {
        // Fetch previous uploads for the current user (or all if admin)
        $uploads = DtrUpload::orderByDesc('created_at')->limit(10)->get();
        return view('admin.dtr', ['uploads' => $uploads]);
    }

    public function store(Request $request, $uploadId)
    {
        $upload = DtrUpload::findOrFail($uploadId);
        $disk = config('filesystems.default', 'local');
        $csvPath = Storage::disk($disk)->path($upload->path);
        $parser = $this->parser;
        $parsed = $parser->parseUploadedFile($csvPath);
        $rows = $parsed['rows'] ?? [];

        foreach ($rows as $row) {
            $entry = new \App\Models\DtrEntry();
            $entry->upload_id = $upload->id;
            $entry->occurred_at = $row['_parsed_date'] ?? null;
            $entry->employee = $row['Emp Name'] ?? $row['emp_name'] ?? $row['Employee'] ?? null;
            $entry->emp_id = $row['Emp ID'] ?? $row['emp_id'] ?? null;
            $entry->time_in = $row['AM-Arrival'] ?? $row['am_arrival'] ?? $row['Time In'] ?? null;
            $entry->time_out = $row['PM-Departure'] ?? $row['pm_departure'] ?? $row['Time Out'] ?? null;
            $entry->raw = $row;
            $entry->save();
        }

        $upload->status = 'stored';
        $upload->save();

        return redirect()->route('dtr')->with('success', 'DTR data has been stored successfully.');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'csv' => 'required|file|mimes:csv,txt',
            'date_column' => 'nullable|string',
            'date_format' => 'nullable|string',
        ]);

        $file = $request->file('csv');

        // store raw file first
        $path = $file->store('dtr_uploads');

        $upload = DtrUpload::create([
            'user_id' => $request->user() ? $request->user()->id : null,
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'size' => $file->getSize(),
            'status' => 'not stored',
        ]);


        // Create a quick preview (first N rows) without processing everything for the user
        try {
            $disk = config('filesystems.default', 'local');
            $previewPath = Storage::disk($disk)->path($path);
            $preview = $this->parser->parseUploadedFile($previewPath, $request->input('date_column'), $request->input('date_format'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to parse CSV for preview: ' . $e->getMessage());
        }

        // Dispatch background job to fully process and persist
        ProcessDtrUpload::dispatch($upload->id, $request->input('date_column'), $request->input('date_format'));

        // Also fetch previous uploads for display
        $uploads = DtrUpload::orderByDesc('created_at')->limit(10)->get();
        return view('admin.dtr', array_merge($preview, [
            'headers' => $preview['headers'] ?? [],
            'upload' => $upload,
            'uploads' => $uploads,
        ]));
    }
}
