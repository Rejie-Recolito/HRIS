<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DtrCsvParser;
use App\Models\DtrUpload;
use App\Jobs\ProcessDtrUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Bus;

class DtrController
{
    protected $parser;

    public function __construct(DtrCsvParser $parser)
    {
        $this->parser = $parser;
    }

    public function show()
    {
        return view('admin.dtr');
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
            'status' => 'pending',
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

        return view('admin.dtr', array_merge($preview, ['headers' => $preview['headers'] ?? [], 'upload' => $upload]));
    }
}
