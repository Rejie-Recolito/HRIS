<?php

namespace App\Jobs;

use App\Models\DtrUpload;
use App\Models\DtrEntry;
use App\Services\DtrCsvParser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessDtrUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uploadId;

    public ?string $dateColumn;
    public ?string $dateFormat;

    public function __construct(int $uploadId, ?string $dateColumn = null, ?string $dateFormat = null)
    {
        $this->uploadId = $uploadId;
        $this->dateColumn = $dateColumn;
        $this->dateFormat = $dateFormat;
    }

    public function handle(DtrCsvParser $parser)
    {
        $upload = DtrUpload::find($this->uploadId);
        if (!$upload) {
            return;
        }

        $upload->status = 'processing';
        $upload->save();

        try {
            $disk = config('filesystems.default', 'local');
            $filePath = storage_path('app/' . $upload->path);
            try {
                // attempt to resolve via storage disk path to support testing fakes
                $filePath = \Illuminate\Support\Facades\Storage::disk($disk)->path($upload->path);
            } catch (\Throwable $e) {
                // fallback to the storage_path
                $filePath = storage_path('app/' . $upload->path);
            }

            $result = $parser->parseUploadedFile($filePath, $this->dateColumn, $this->dateFormat);

            $rows = $result['rows'] ?? [];

            $chunk = [];
            $now = now();
            foreach ($rows as $r) {
                $chunk[] = [
                    'upload_id' => $upload->id,
                    'occurred_at' => isset($r['_parsed_date']) && $r['_parsed_date'] ? $r['_parsed_date']->toDateTimeString() : null,
                    'employee' => is_array($r) ? ($r['Employee'] ?? ($r['employee'] ?? null)) : null,
                    'time_in' => is_array($r) ? ($r['TimeIn'] ?? ($r['time_in'] ?? null)) : null,
                    'time_out' => is_array($r) ? ($r['TimeOut'] ?? ($r['time_out'] ?? null)) : null,
                    'raw' => json_encode($r),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($chunk) >= 500) {
                    DB::table('dtr_entries')->insert($chunk);
                    $chunk = [];
                }
            }

            if (count($chunk)) {
                DB::table('dtr_entries')->insert($chunk);
            }

            $upload->status = 'completed';
            $upload->save();
        } catch (\Exception $e) {
            $upload->status = 'failed';
            $upload->error = $e->getMessage();
            $upload->save();
            throw $e;
        }
    }
}
