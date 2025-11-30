<?php
// CLI helper to test DOCX template population for a given emp_id and month
// Usage: php scripts/dtr_template_test.php EMP_ID YYYY-MM

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DtrEntry;
use App\Services\PhilippinesHolidays;
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;

$empId = $argv[1] ?? null;
$month = $argv[2] ?? null;
if (!$empId || !$month) {
    echo "Usage: php scripts/dtr_template_test.php EMP_ID YYYY-MM\n";
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

// Resolve employee name similar to controller
$empName = null;
foreach ($entries as $day => $dayEntries) {
    foreach ($dayEntries as $ent) {
        $raw = $ent->raw ?? null;
        if (is_string($raw)) {
            $tmp = @json_decode($raw, true);
            if (is_array($tmp)) $raw = $tmp;
        }
        if (is_array($raw)) {
            foreach (['Emp Name','EmpName','Emp','Employee Name','EmployeeName','emp_name','empname'] as $k) {
                if (array_key_exists($k, $raw) && trim($raw[$k]) !== '') {
                    $empName = trim($raw[$k]);
                    break 3;
                }
            }
            foreach ($raw as $rk => $rv) {
                if (in_array(strtolower($rk), ['emp name','empname','emp','employee name','employeename','emp_name','empname']) && trim($rv) !== '') {
                    $empName = trim($rv);
                    break 3;
                }
            }
        }
    }
}
if (!$empName) {
    $empModel = \App\Models\Employee::where('employee_id', $empId)->first();
    if ($empModel) {
        $empName = trim(($empModel->firstname ?? '') . ' ' . ($empModel->middlename ? ($empModel->middlename . ' ') : '') . ($empModel->lastname ?? '')) ?: $empId;
    }
}
if (!$empName) $empName = $empId;

// Compute regular days (same logic)
$totalWorkingDays = 0;
foreach (range(1, $daysInMonth) as $d) {
    $dayEntries = $entries->get($d, collect());
    if ($dayEntries->isEmpty()) continue;
    $countable = false;
    foreach ($dayEntries as $ent) {
        if (!empty($ent->time_in) || !empty($ent->time_out)) { $countable = true; break; }
        $raw = $ent->raw ?? null;
        if (is_string($raw)) {
            $tmp = @json_decode($raw, true);
            if (is_array($tmp)) $raw = $tmp;
        }
        if (is_array($raw)) {
            foreach ($raw as $k => $v) {
                if ($v !== null && $v !== '') { $countable = true; break 2; }
            }
        }
    }
    if ($countable) $totalWorkingDays++;
}

// Flatten entries and compute undertime totals using controller helper if available, else replicate simple
$allEntriesFlat = collect($entries)->flatten(1);
// We'll call the controller helper if exists
// local copy of undertime totals computation (same logic as controller)
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
            $explicitHours = null;
            $explicitMinutes = null;
            foreach ($raw as $k => $v) {
                if ($v === null || $v === '') continue;
                $lk = strtolower($k);
                if (strpos($lk, 'undertime') === false) continue;
                if (preg_match('/hour|hr|hrs/i', $lk)) { $explicitHours = $v; continue; }
                if (preg_match('/min|minute|mins/i', $lk)) { $explicitMinutes = $v; continue; }
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

$undertimeTotals = computeUndertimeTotalsLocal($allEntriesFlat);

// Holidays for month
$holidays = PhilippinesHolidays::getHolidaysForMonth((int)$start->year, (int)$start->month);

$templatePath = resource_path('templates/dtr_template.docx');
if (!file_exists($templatePath)) {
    echo "Template not found at: {$templatePath}\n";
    exit(2);
}

$tmpDir = storage_path('app/tmp');
if (!file_exists($tmpDir)) mkdir($tmpDir, 0755, true);
$outPath = $tmpDir . DIRECTORY_SEPARATOR . 'dtr_template_test_' . preg_replace('/[^A-Za-z0-9-_]/','_', $empId) . '_' . $month . '_' . time() . '_' . uniqid() . '.docx';

// Populate template
$tpl = new TemplateProcessor($templatePath);
$tpl->setValue('EMP_ID', $empId);
$tpl->setValue('EMP_NAME', $empName);
$tpl->setValue('MONTH', $start->format('F'));
$tpl->setValue('REGULAR_DAYS', (string)$totalWorkingDays);
$tpl->setValue('TOTAL_UNDERTIME_HOURS', (string)($undertimeTotals['hours'] ?? 0));
$tpl->setValue('TOTAL_UNDERTIME_MINUTES', (string)($undertimeTotals['minutes'] ?? 0));
$tpl->setValue('HOLIDAYS_LIST', implode(', ', array_values($holidays)) ?: '');
// also set alternative global placeholder used in template
$tpl->setValue('HOLIDAY LIST', implode(', ', array_values($holidays)) ?: '');
$tpl->cloneRow('day', $daysInMonth);

for ($i = 1; $i <= $daysInMonth; $i++) {
    $dayEntries = $entries->get($i, collect());
    $amArrival = '';
    $amDeparture = '';
    $pmArrival = '';
    $pmDeparture = '';
    foreach ($dayEntries as $ent) {
        $raw = $ent->raw ?? null;
        if (is_string($raw)) {
            $tmp = @json_decode($raw, true);
            if (is_array($tmp)) $raw = $tmp;
        }
        if (is_array($raw)) {
            foreach (['AM-Arrival','AM Arrival','am_arrival','AM_Arrival'] as $k) {
                if (array_key_exists($k, $raw) && $raw[$k] !== '') { $amArrival = $raw[$k]; break; }
            }
        }
        $occur = Carbon::parse($ent->occurred_at);
        if (!$amArrival && !empty($ent->time_in)) $amArrival = $ent->time_in;
    }
    $tpl->setValue("day#{$i}", $i);
    $tpl->setValue("am_arrival#{$i}", $amArrival);
    $tpl->setValue("am_departure#{$i}", $amDeparture);
    $tpl->setValue("pm_arrival#{$i}", $pmArrival);
    $tpl->setValue("pm_departure#{$i}", $pmDeparture);
    // daily undertime (local helper)
    $dailyTotals = computeUndertimeTotalsLocal($dayEntries);
    $tpl->setValue("undertime_hours#{$i}", (string)($dailyTotals['hours'] ?? 0));
    $tpl->setValue("undertime_minutes#{$i}", (string)($dailyTotals['minutes'] ?? 0));
    $tpl->setValue("HOLIDAY#{$i}", $holidays[$i] ?? '');
    $tpl->setValue("HOLIDAY LIST#{$i}", $holidays[$i] ?? '');
}

$tpl->saveAs($outPath);
echo "Generated: {$outPath}\n";
exit(0);
