<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DtrCsvParser;
use App\Models\DtrUpload;
use App\Jobs\ProcessDtrUpload;
use Illuminate\Support\Facades\Storage;


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

        // Debug: Show available employee_ids
        $allEmpIds = \App\Models\Employee::pluck('employee_id')->toArray();
        \Log::info('Searching for emp_id: ' . $empId);
        \Log::info('Available employee_ids: ' . implode(',', $allEmpIds));

        // Find employee by employee_id
        $employee = \App\Models\Employee::where('employee_id', $empId)->first();
        if (!$employee) {
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
