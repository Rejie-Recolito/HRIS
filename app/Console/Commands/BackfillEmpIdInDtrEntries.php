<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DtrEntry;

class BackfillEmpIdInDtrEntries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dtr:backfill-empid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill emp_id in dtr_entries from raw JSON column';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = 0;
        DtrEntry::chunk(500, function ($entries) use (&$count) {
            foreach ($entries as $entry) {
                $raw = $entry->raw;
                $empId = $raw['Emp ID'] ?? $raw['emp_id'] ?? null;
                if ($empId && $entry->emp_id !== $empId) {
                    $entry->emp_id = $empId;
                    $entry->save();
                    $count++;
                }
            }
        });
        $this->info("Backfilled emp_id for {$count} DTR entries.");
        return 0;
    }
}
