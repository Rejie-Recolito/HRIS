<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceRecord;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AdminNotification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Employee;
use App\Models\ServiceRecordRequest;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;

class ServiceRecordController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'age' => 'required|integer',
            'salary' => 'required|numeric',
            'date_of_birth' => 'required|date',
            'job_title' => 'required|string',
            'place_of_birth' => 'required|string',
            'office' => 'required|string',
            'status' => 'required|string',
            'date_of_service' => 'required|date',
            'place_of_assignment' => 'required|string',
        ]);

        $serviceRecord = ServiceRecord::create(array_merge($validated, [
            'user_id' => Auth::id(),
        ]));


        // Notify admin with a custom message
        $admin = User::where('is_admin', true)->first();
        if ($admin) {
            $admin->notifications()->create([
                'id' => (string) Str::uuid(),
                'type' => AdminNotification::class,
                'data' => [
                    'message' => Auth::user()->name . ' has requested a service record.',
                    'service_record_id' => $serviceRecord->id,
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Service record created successfully.');
    }

    /**
     * Export all service records for a user into a DOCX table and download.
     */
    public function exportDocx($userId)
    {
        $user = User::findOrFail($userId);
        $serviceRecords = ServiceRecord::where('user_id', $user->id)->orderBy('service_from')->get();
        $employee = Employee::where('user_id', $user->id)->first();

        // If a .docx template exists, use TemplateProcessor to fill it exactly
        // Template expectations:
        // - Place the template at resources/templates/service_record_template.docx
    // - The template should include a single table row with placeholders like:
    //   ${from}, ${to}, ${rank}, ${designation}, ${status}, ${monthly_pay}, ${station}, ${branch}, ${leave_of_absence}, ${sep_date}, ${sep_cause}
        // - TemplateProcessor::cloneRow('from', $n) will be used; the code sets values using keys like from#1, rank#1, etc.
        $templatePath = resource_path('templates/service_record_template.docx');
        $altTemplatePath = resource_path('templates/Service-Record-template.docx');
        // accept either normalized or original uploaded filename
        if (!file_exists($templatePath) && file_exists($altTemplatePath)) {
            $templatePath = $altTemplatePath;
        }

        if (file_exists($templatePath)) {
            try {
                $tpl = new TemplateProcessor($templatePath);

                $n = max(1, $serviceRecords->count());
                // cloneRow expects the template to have placeholders like ${from#1} in the row
                $tpl->cloneRow('from', $n);

                $fmt = function ($d) {
                    if (!$d) return '';
                    try {
                        return \Carbon\Carbon::parse($d)->format('Y-m-d');
                    } catch (\Exception $e) {
                        return (string) $d;
                    }
                };

                for ($i = 1; $i <= $n; $i++) {
                    $rec = $serviceRecords->get($i - 1);
                    $tpl->setValue("from#{$i}", $rec ? $fmt($rec->service_from) : '');
                    $tpl->setValue("to#{$i}", $rec ? $fmt($rec->service_to) : '');
                    $tpl->setValue("rank#{$i}", $rec->appointment_rank ?? '');
                    $tpl->setValue("designation#{$i}", $rec->appointment_designation ?? '');
                    $tpl->setValue("status#{$i}", $rec->appointment_status ?? '');
                    $tpl->setValue("monthly_pay#{$i}", $rec && $rec->appointment_monthly_base_pay ? number_format($rec->appointment_monthly_base_pay, 2) : '');
                    $tpl->setValue("station#{$i}", $rec->station ?? '');
                    $tpl->setValue("branch#{$i}", $rec->place_of_assignment ?? '');
                    // Use single 'leave_of_absence' field
                    $tpl->setValue("leave_of_absence#{$i}", $rec->leave_of_absence ?? '');
                    $tpl->setValue("sep_date#{$i}", $rec ? $fmt($rec->separation_date) : '');
                    $tpl->setValue("sep_cause#{$i}", $rec->separation_cause ?? '');
                }

                // Header placeholders (if template contains them)
                // Format name as: surname, firstname middlename
                $tpl->setValue('name', $employee ? trim(sprintf('%s, %s %s', $employee->lastname ?? '', $employee->firstname ?? '', $employee->middlename ?? '')) : ($user->name ?? ''));
                $tpl->setValue('birth', $employee->date_of_birth ?? ($user->date_of_birth ?? ''));
                $tpl->setValue('place_of_birth', $employee->place_of_birth ?? ($user->place_of_birth ?? ''));

                $tempFile = tempnam(sys_get_temp_dir(), 'srvrec') . '.docx';
                $tpl->saveAs($tempFile);

                return response()->download($tempFile, 'service_record_' . $user->id . '_' . date('Ymd_His') . '.docx')->deleteFileAfterSend(true);
            } catch (\Exception $e) {
                // fallback to programmatic generation on error
                // continue to the PhpWord builder below
            }
        }

        $phpWord = new PhpWord();
        // Default font
        $phpWord->getDefaultFontName('Times New Roman');
        $phpWord->getDefaultFontSize(12);

        $section = $phpWord->addSection([
            'orientation' => 'landscape',
            'marginTop' => 600,
            'marginBottom' => 600,
            'marginLeft' => 600,
            'marginRight' => 600,
        ]);

        $section->addText('SERVICE RECORD', ['bold' => true, 'size' => 18, 'name' => 'Times New Roman'], ['alignment' => 'center']);
        $section->addText('(To be accomplished by Employer)', ['size' => 10, 'name' => 'Times New Roman'], ['alignment' => 'center']);
        $section->addTextBreak(1);

        // Add user header lines similar to the template
    // Use employee profile when available for more accurate header data
    $headerName = $employee ? trim(sprintf('%s, %s %s', $employee->lastname ?? '', $employee->firstname ?? '', $employee->middlename ?? '')) : ($user->name ?? '');
    $headerBirth = $employee->date_of_birth ?? ($user->date_of_birth ?? '');
    $headerPlace = $employee->place_of_birth ?? ($user->place_of_birth ?? '');

    // Header table to mimic underlined fields
    $hdrTableStyle = ['borderSize' => 0, 'cellMargin' => 0];
    $phpWord->addTableStyle('HdrTable', $hdrTableStyle);
    $hdr = $section->addTable('HdrTable');

    // Name row: label + long underline cell
    $hdr->addRow();
    $hdr->addCell(1000)->addText('NAME:', ['bold' => true, 'name' => 'Times New Roman']);
    $hdr->addCell(14000, ['borderBottomSize' => 6])->addText($headerName, ['name' => 'Times New Roman']);
    $hdr->addCell(2000)->addText('', []);

    // Birth row
    $hdr->addRow();
    $hdr->addCell(1000)->addText('BIRTH:', ['bold' => true, 'name' => 'Times New Roman']);
    $hdr->addCell(14000, ['borderBottomSize' => 6])->addText($headerBirth, ['name' => 'Times New Roman']);
    $hdr->addCell(2000)->addText('(Data herein should be checked)', ['size' => 9, 'name' => 'Times New Roman']);

    // Place of birth row
    $hdr->addRow();
    $hdr->addCell(1000)->addText('PLACE OF BIRTH:', ['bold' => true, 'name' => 'Times New Roman']);
    $hdr->addCell(16000, ['borderBottomSize' => 6])->addText($headerPlace, ['name' => 'Times New Roman']);
    $section->addTextBreak(1);

        // Define table style
        $tableStyle = [
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 60,
        ];
        $firstRowStyle = ['bgColor' => 'FFFFFF'];
        $phpWord->addTableStyle('ServiceTable', $tableStyle, $firstRowStyle);

    $table = $section->addTable('ServiceTable');

    // First header row with merged cells (using specific widths)
    $table->addRow();
    $table->addCell(1800, ['gridSpan' => 2, 'valign' => 'center'])->addText('SERVICE\n(Inclusive Dates)', ['bold' => true, 'name' => 'Times New Roman'], ['alignment' => 'center']);
    $table->addCell(6800, ['gridSpan' => 4, 'valign' => 'center'])->addText('RECORD OF APPOINTMENT', ['bold' => true, 'name' => 'Times New Roman'], ['alignment' => 'center']);
    $table->addCell(2600, ['gridSpan' => 2, 'valign' => 'center'])->addText('OFFICE ENTITY/DIVISION', ['bold' => true, 'name' => 'Times New Roman'], ['alignment' => 'center']);
    $table->addCell(1400, ['valign' => 'center'])->addText('LEAVE of\nAbsence\nw/o Pay', ['bold' => true, 'name' => 'Times New Roman'], ['alignment' => 'center']);
    $table->addCell(1600, ['gridSpan' => 2, 'valign' => 'center'])->addText('Separation', ['bold' => true, 'name' => 'Times New Roman'], ['alignment' => 'center']);

    // Second header row with specific column labels
    $table->addRow();
    $table->addCell(900)->addText('From', ['bold' => true, 'name' => 'Times New Roman'], ['alignment' => 'center']);
    $table->addCell(900)->addText('To', ['bold' => true, 'name' => 'Times New Roman'], ['alignment' => 'center']);

    $table->addCell(900)->addText('Rank', ['bold' => true, 'name' => 'Times New Roman'], ['alignment' => 'center']);
    $table->addCell(2300)->addText('Designation', ['bold' => true, 'name' => 'Times New Roman'], ['alignment' => 'center']);
    $table->addCell(900)->addText('Status', ['bold' => true, 'name' => 'Times New Roman'], ['alignment' => 'center']);
    $table->addCell(900)->addText('Monthly\nBase Pay', ['bold' => true, 'name' => 'Times New Roman'], ['alignment' => 'center']);

    $table->addCell(1800)->addText('Station/Place', ['bold' => true, 'name' => 'Times New Roman'], ['alignment' => 'center']);
    $table->addCell(1200)->addText('Branch', ['bold' => true, 'name' => 'Times New Roman'], ['alignment' => 'center']);

    // Leave of Absence: single column (uses leave_of_absence or falls back to legacy from/to)
    $table->addCell(1800)->addText('Leave of Absence', ['bold' => true, 'name' => 'Times New Roman'], ['alignment' => 'center']);

    $table->addCell(800)->addText('Date', ['bold' => true, 'name' => 'Times New Roman'], ['alignment' => 'center']);
    $table->addCell(1800)->addText('Cause', ['bold' => true, 'name' => 'Times New Roman'], ['alignment' => 'center']);

        // Helper to safely format date-like values
        $fmt = function ($d) {
            if (!$d) return '';
            try {
                return \Carbon\Carbon::parse($d)->format('Y-m-d');
            } catch (\Exception $e) {
                return (string) $d;
            }
        };

        // Render a fixed number of rows to match the printed template appearance
        $rowsToRender = max(12, $serviceRecords->count());
        for ($i = 0; $i < $rowsToRender; $i++) {
            $rec = $serviceRecords->get($i);
            $table->addRow();
            $table->addCell(1500)->addText($rec ? $fmt($rec->service_from) : '');
            $table->addCell(1500)->addText($rec ? $fmt($rec->service_to) : '');

            $table->addCell(1200)->addText($rec->appointment_rank ?? '');
            $table->addCell(3000)->addText($rec->appointment_designation ?? '');
            $table->addCell(1200)->addText($rec->appointment_status ?? '');
            $table->addCell(1200)->addText($rec && $rec->appointment_monthly_base_pay ? number_format($rec->appointment_monthly_base_pay, 2) : '');

            $table->addCell(2000)->addText($rec->station ?? '');
            $table->addCell(1500)->addText($rec->place_of_assignment ?? '');

            // Leave of Absence value
            $table->addCell(2500)->addText($rec->leave_of_absence ?? '');

            $table->addCell(1200)->addText($rec ? $fmt($rec->separation_date) : '');
            $table->addCell(1800)->addText($rec->separation_cause ?? '');
        }

        $fileName = 'service_record_' . $user->id . '_' . date('Ymd_His') . '.docx';

        // Save to temporary file and return download
        $tempFile = tempnam(sys_get_temp_dir(), 'srvrec') . '.docx';
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Show request board for admins.
     */
    public function requestsIndex()
    {
        // Show pending and in-progress requests so admins can resume incomplete ones
        $requests = ServiceRecordRequest::with('user')
            ->whereIn('request_status', ['pending', 'in_progress'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.service_record_requests', compact('requests'));
    }

    /**
     * Show history (accepted/declined) for admin.
     */
    public function historyIndex()
    {
        $requests = ServiceRecordRequest::with('user')
            ->whereIn('request_status', ['accepted', 'deleted', 'declined'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.service_record_requests_history', compact('requests'));
    }

    /**
     * Admin accepts a request: create actual ServiceRecord (prefilling from Employee) and redirect to edit.
     */
    public function acceptRequest($id)
    {
        $req = ServiceRecordRequest::findOrFail($id);
        $user = $req->user;

        $serviceRecord = null;
        if ($user) {
            $employee = Employee::where('user_id', $user->id)->first();
            // create a minimal service record (DB columns are nullable now)
            $data = [
                'user_id' => $user->id,
                'name' => $req->name ?? $user->name,
            ];

            if ($employee) {
                $data = array_merge($data, [
                    'age' => $employee->age,
                    'salary' => $employee->salary,
                    'date_of_birth' => $employee->date_of_birth,
                    'job_title' => $employee->designation ?? $employee->job_title,
                    'place_of_birth' => $employee->place_of_birth,
                    'office' => $employee->place_of_assignment,
                    'status' => $employee->status,
                    'date_of_service' => $employee->start_date,
                    'place_of_assignment' => $employee->place_of_assignment,
                ]);
            }

            $serviceRecord = ServiceRecord::create($data);
        }

    // mark the request as in_progress and link the created service record
    $req->request_status = 'in_progress';
    $req->service_record_id = $serviceRecord ? $serviceRecord->id : null;
    $req->save();

        if ($serviceRecord) {
            return redirect()->route('service-records.edit', ['id' => $serviceRecord->id]);
        }
        return redirect()->back()->with('success', 'Request accepted.');
    }

    /**
     * Admin deletes a request.
     */
    public function destroyRequest($id)
    {
        $req = ServiceRecordRequest::findOrFail($id);
        $req->delete();
        // Redirect to the requests index to avoid ending up on a deleted resource URL
        return redirect()->route('service-record-requests.index')->with('success', 'Request deleted.');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $serviceRecords = ServiceRecord::all();
        return view('admin.service_record', compact('serviceRecords'));
    }

    /**
     * Update the status of a service record.
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,ready',
        ]);

        $serviceRecord = ServiceRecord::findOrFail($id);
        $serviceRecord->request_status = $validated['status'];
        $serviceRecord->save();

        return response()->json(['message' => 'Status updated successfully']);
    }

    /**
     * Show edit form for admin to complete a service record request.
     */
    public function edit($id)
    {
    $serviceRecord = ServiceRecord::findOrFail($id);
    // Load all service records for the same user (for the sidebar list)
    $serviceRecords = $serviceRecord->user_id ? ServiceRecord::where('user_id', $serviceRecord->user_id)->orderBy('service_from','desc')->get() : collect([$serviceRecord]);
    return view('admin.employee_service_record', compact('serviceRecord', 'serviceRecords'));
    }

    /**
     * Update service record details (admin action).
     */
    public function update(Request $request, $id)
    {
        $serviceRecord = ServiceRecord::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string',
            'age' => 'nullable|integer',
            'salary' => 'nullable|numeric',
            'date_of_birth' => 'nullable|date',
            'job_title' => 'nullable|string',
            'place_of_birth' => 'nullable|string',
            'office' => 'nullable|string',
            'status' => 'nullable|string',
            // Admin-filled fields
            'service_from' => 'nullable|date',
            'service_to' => 'nullable|date',
            'appointment_rank' => 'nullable|string',
            'appointment_designation' => 'nullable|string',
            'appointment_status' => 'nullable|string',
            'appointment_monthly_base_pay' => 'nullable|numeric',
            'station' => 'nullable|string',
            'place' => 'nullable|string',
            // Leave of Absence (single field)
            'leave_of_absence' => 'nullable|string|max:255',
            'separation_date' => 'nullable|date',
            'separation_cause' => 'nullable|string',
            'date_of_service' => 'nullable|date',
            'place_of_assignment' => 'nullable|string',
            'request_status' => 'nullable|string|in:pending,ready',
        ]);

        $serviceRecord->update($validated);

        // If there's a linked request in progress, mark it accepted (admin finished the form)
        try {
            $req = ServiceRecordRequest::where('service_record_id', $serviceRecord->id)
                ->where('request_status', 'in_progress')
                ->first();
            if ($req) {
                // Admin finished the form — remove the original request from the queue
                $req->delete();
            }
        } catch (\Throwable $e) {
            // non-fatal: continue
        }

    return redirect()->route('service-record-requests.index')->with('success', 'Service record updated.');
    }

    /**
     * Append partial changes to a service record without completing the request.
     * This allows the admin to add information multiple times while keeping the request in_progress.
     */
    public function append(Request $request, $id)
    {
        $serviceRecord = ServiceRecord::findOrFail($id);

        $validated = $request->validate([
            'service_from' => 'nullable|date',
            'service_to' => 'nullable|date',
            'appointment_rank' => 'nullable|string',
            'appointment_designation' => 'nullable|string',
            'appointment_status' => 'nullable|string',
            'appointment_monthly_base_pay' => 'nullable|numeric',
            'station' => 'nullable|string',
            'place' => 'nullable|string',
            'leave_of_absence' => 'nullable|string|max:255',
            'separation_date' => 'nullable|date',
            'separation_cause' => 'nullable|string',
            'name' => 'nullable|string',
            'age' => 'nullable|integer',
            'job_title' => 'nullable|string',
            'office' => 'nullable|string',
            'salary' => 'nullable|numeric',
            'place_of_assignment' => 'nullable|string',
        ]);

        // Merge validated fields into the existing record (keep other fields intact)
        $serviceRecord->update(array_filter($validated, function ($v) { return $v !== null; }));

        return redirect()->route('service-records.edit', ['id' => $serviceRecord->id])->with('success', 'Service record updated (partial).');
    }

    /**
     * Show the service record form for the authenticated user.
     */
    public function show()
    {
    /** @var \App\Models\User|null $user */
    $user = Auth::user();
    $serviceRecords = $user ? $user->serviceRecords()->latest()->get() : collect();
        return view('service_record_user', compact('serviceRecords'));
    }

    /**
     * Admin accepts a request: prefill with employee data when available and redirect to edit.
     */
    public function accept($id)
    {
        $serviceRecord = ServiceRecord::findOrFail($id);
        $user = $serviceRecord->user;

        if ($user) {
            $employee = Employee::where('user_id', $user->id)->first();
            if ($employee) {
                // fill only missing fields
                // Format name as: surname, firstname middlename
                $serviceRecord->name = $serviceRecord->name ?? trim(sprintf('%s, %s %s', $employee->lastname ?? '', $employee->firstname ?? '', $employee->middlename ?? ''));
                $serviceRecord->age = $serviceRecord->age ?? $employee->age;
                $serviceRecord->salary = $serviceRecord->salary ?? $employee->salary;
                $serviceRecord->date_of_birth = $serviceRecord->date_of_birth ?? $employee->date_of_birth;
                $serviceRecord->job_title = $serviceRecord->job_title ?? ($employee->designation ?? $employee->job_title);
                $serviceRecord->place_of_birth = $serviceRecord->place_of_birth ?? $employee->place_of_birth;
                $serviceRecord->office = $serviceRecord->office ?? $employee->place_of_assignment;
                $serviceRecord->status = $serviceRecord->status ?? $employee->status;
                $serviceRecord->date_of_service = $serviceRecord->date_of_service ?? $employee->start_date;
                $serviceRecord->place_of_assignment = $serviceRecord->place_of_assignment ?? $employee->place_of_assignment;
                $serviceRecord->save();
            }
        }

        return redirect()->route('service-records.edit', ['id' => $serviceRecord->id]);
    }

    /**
     * Admin deletes a service record request.
     */
    public function destroy($id)
    {
        $serviceRecord = ServiceRecord::findOrFail($id);
        $serviceRecord->delete();
        return redirect()->back()->with('success', 'Service record request deleted.');
    }

    /**
     * Create a service record request on behalf of the authenticated user.
     */
    public function requestByUser(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json(['message' => 'Unauthenticated'], 401);
                }
                return redirect()->route('login');
            }

            // Try to fill from Employee profile if available
            $employee = Employee::where('user_id', $user->id)->first();

            // Create a lightweight request record instead of a full service record
            $name = $employee ? trim(sprintf('%s, %s %s', $employee->lastname ?? '', $employee->firstname ?? '', $employee->middlename ?? '')) : $user->name;
            $req = ServiceRecordRequest::create([
                'user_id' => $user->id,
                'name' => $name,
                'request_status' => 'pending',
            ]);

            // Notify admin
            $admin = User::where('is_admin', true)->first();
            if ($admin) {
                $admin->notifications()->create([
                    'id' => (string) Str::uuid(),
                    'type' => AdminNotification::class,
                    'data' => [
                        'message' => $user->name . ' has requested a service record.',
                        'service_record_request_id' => $req->id,
                    ],
                ]);
            }

            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['message' => 'Service record request submitted.', 'id' => $req->id], 201);
            }

            return redirect()->back()->with('success', 'Service record request submitted.');
        } catch (\Exception $e) {
            // On exception return JSON if AJAX or rethrow
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['message' => 'Server error', 'error' => $e->getMessage()], 500);
            }
            throw $e;
        }
    }
}