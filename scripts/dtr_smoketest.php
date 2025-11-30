<?php
// Simple smoke test for DTR undertime totals
// Usage: php scripts/dtr_smoketest.php EMP_ID YYYY-MM

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DtrEntry;
use Carbon\Carbon;

$empId = $argv[1] ?? null;
$month = $argv[2] ?? null;
if (!$empId || !$month) {
    echo "Usage: php scripts/dtr_smoketest.php EMP_ID YYYY-MM\n";
    exit(1);
}

try {
    $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
} catch (Exception $e) {
    echo "Invalid month format. Use YYYY-MM.\n";
    exit(1);
}
$daysInMonth = $start->daysInMonth;

$entries = DtrEntry::where('emp_id', $empId)
    ->where('occurred_at', 'like', $month . '%')
    ->orderBy('occurred_at')
    ->get()
    ->groupBy(function ($e) {
        return Carbon::parse($e->occurred_at)->day;
    });

function computeUndertimeTotalsLocal($entries)
{
    $totalMinutes = 0;
    if (!$entries) return ['hours' => 0, 'minutes' => 0, 'total_minutes' => 0];
    foreach ($entries as $entry) {
        $raw = $entry->raw ?? null;
        if (is_string($raw)) {
            $tmp = @json_decode($raw, true);
            if (is_array($tmp)) $raw = $tmp;
        }

        $hrs = 0.0;
        $mins = 0;

        if (is_array($raw)) {
            // First, try to find explicit undertime hours/minutes keys and prefer them
            $explicitHours = null;
            $explicitMinutes = null;
            foreach ($raw as $k => $v) {
                if ($v === null || $v === '') continue;
                $lk = strtolower($k);
                if (strpos($lk, 'undertime') === false) continue;
                // look for explicit hour/minute qualifiers in the key name
                if (preg_match('/hour|hr|hrs/i', $lk)) {
                    $explicitHours = $v;
                    continue;
                }
                if (preg_match('/min|minute|mins/i', $lk)) {
                    $explicitMinutes = $v;
                    continue;
                }
                if (strpos($lk, 'undertime_hours') !== false) $explicitHours = $v;
                if (strpos($lk, 'undertime_minutes') !== false) $explicitMinutes = $v;
            }

            if ($explicitHours !== null || $explicitMinutes !== null) {
                if ($explicitHours !== null) {
                    if (is_numeric($explicitHours)) {
                        $hrs = floatval($explicitHours);
                    } elseif (is_string($explicitHours) && preg_match('/^(\d+)\s*[:h]\s*(\d+)?/i', trim($explicitHours), $m)) {
                        $hrs = intval($m[1]);
                        $mins += isset($m[2]) ? intval($m[2]) : 0;
                    } else {
                        $num = floatval(preg_replace('/[^0-9.]/', '', $explicitHours));
                        if ($num > 0) $hrs = $num;
                    }
                }
                if ($explicitMinutes !== null) {
                    if (is_numeric($explicitMinutes)) {
                        $mins += intval(round(floatval($explicitMinutes)));
                    } elseif (is_string($explicitMinutes) && preg_match('/^(\d+)\s*[:h]\s*(\d+)?/i', trim($explicitMinutes), $m)) {
                        $mins += isset($m[2]) ? intval($m[2]) : intval($m[1]);
                    } else {
                        $num = intval(round(floatval(preg_replace('/[^0-9.]/', '', $explicitMinutes))));
                        if ($num > 0) $mins += $num;
                    }
                }
            } else {
                foreach ($raw as $k => $v) {
                    if ($v === null || $v === '') continue;
                    $lk = strtolower($k);
                    if (strpos($lk, 'undertime') === false) continue;

                    if (is_numeric($v)) {
                        $num = floatval($v);
                        if ($num > 0 && $num <= 5) {
                            $hrs += $num;
                        } else {
                            $mins += intval(round($num));
                        }
                        continue;
                    }

                    if (is_string($v)) {
                        if (preg_match('/^(\d+)\s*[:h]\s*(\d+)?/i', trim($v), $m)) {
                            $h = intval($m[1]);
                            $m2 = isset($m[2]) ? intval($m[2]) : 0;
                            $hrs += $h;
                            $mins += $m2;
                            continue;
                        }
                        $num = floatval(preg_replace('/[^0-9.]/', '', $v));
                        if ($num > 0) {
                            if ($num <= 5) {
                                $hrs += $num;
                            } else {
                                $mins += intval(round($num));
                            }
                        }
                    }
                }
            }
        }

        $entryMinutes = intval(round($hrs * 60)) + intval($mins);
        $totalMinutes += $entryMinutes;
    }
    $totalHours = intdiv($totalMinutes, 60);
    $remMinutes = $totalMinutes % 60;
    return ['hours' => $totalHours, 'minutes' => $remMinutes, 'total_minutes' => $totalMinutes];
}

echo "Smoke test for emp_id={$empId}, month={$month}\n";

echo str_repeat('=',60) . "\n";
for ($d = 1; $d <= $daysInMonth; $d++) {
    $dayEntries = $entries->get($d, collect());
    $count = $dayEntries->count();
    $dailyTotals = computeUndertimeTotalsLocal($dayEntries);
    echo sprintf("Day %2d: entries=%2d  undertime = %2d hrs %2d mins\n", $d, $count, $dailyTotals['hours'], $dailyTotals['minutes']);
}

$allEntriesFlat = collect($entries)->flatten(1);
$monthly = computeUndertimeTotalsLocal($allEntriesFlat);

echo str_repeat('=',60) . "\n";
echo "Monthly total undertime: {$monthly['hours']} hrs {$monthly['minutes']} mins  (total_minutes={$monthly['total_minutes']})\n";

// Also show simple sum of per-day minutes to cross-check
$sumPerDayMinutes = 0;
for ($d = 1; $d <= $daysInMonth; $d++) {
    $dailyTotals = computeUndertimeTotalsLocal($entries->get($d, collect()));
    $sumPerDayMinutes += $dailyTotals['total_minutes'];
}

echo "Sum of per-day totals (minutes): {$sumPerDayMinutes}\n";

// Print counts
$totalEntries = $allEntriesFlat->count();
echo "Total DtrEntry rows considered: {$totalEntries}\n";

exit(0);
