<?php
namespace App\Services;

use Carbon\Carbon;

class PhilippinesHolidays
{
    /**
     * Return an associative array of day => holiday name for the given year and month.
     * Day is integer day of month (1..31). Only holidays falling in the given month are returned.
     *
     * @param int $year
     * @param int $month
     * @return array<int,string>
     */
    public static function getHolidaysForMonth(int $year, int $month): array
    {
        $all = self::getAllHolidays($year);
        $out = [];
        foreach ($all as $ymd => $name) {
            try {
                $d = Carbon::parse($ymd);
            } catch (\Exception $e) {
                continue;
            }
            if ((int)$d->year === $year && (int)$d->month === $month) {
                $out[(int)$d->day] = $name;
            }
        }
        return $out;
    }

    /**
     * Return a map of Y-m-d => holiday name for known Philippine holidays for the given year.
     * This includes fixed-date holidays and common moveable holidays (Easter related) and a few observed rules.
     * Not exhaustive; intended for DTR display and documents.
     *
     * @param int $year
     * @return array<string,string>
     */
    public static function getAllHolidays(int $year): array
    {
        $holidays = [];

        // Fixed-date holidays
        $holidays[sprintf('%04d-01-01', $year)] = 'New Year\'s Day';
        $holidays[sprintf('%04d-04-09', $year)] = 'Araw ng Kagitingan'; // Day of Valor
        $holidays[sprintf('%04d-05-01', $year)] = 'Labor Day';
        $holidays[sprintf('%04d-06-12', $year)] = 'Independence Day';
        $holidays[sprintf('%04d-08-21', $year)] = 'Ninoy Aquino Day';
        $holidays[sprintf('%04d-11-30', $year)] = 'Bonifacio Day';
        $holidays[sprintf('%04d-12-25', $year)] = 'Christmas Day';
        $holidays[sprintf('%04d-12-30', $year)] = 'Rizal Day';

        // Additional/common special non-working days (may change by proclamation) — include common ones
        $holidays[sprintf('%04d-08-27', $year)] = 'National Heroes Day';

        // Moveable holidays: Good Friday and Easter Sunday (Philippines observes Maundy Thursday/Good Friday)
        list($easterY, $easterM, $easterD) = self::easterDate($year);
        $easter = Carbon::createFromDate($easterY, $easterM, $easterD)->startOfDay();
        $goodFriday = $easter->copy()->subDays(2);
        $maundy = $easter->copy()->subDays(3);
        $holidays[$maundy->toDateString()] = 'Maundy Thursday';
        $holidays[$goodFriday->toDateString()] = 'Good Friday';
        $holidays[$easter->toDateString()] = 'Easter Sunday';

        // Eid al-Fitr and Eid al-Adha are not included (they vary by lunar calendar and proclamation)

        // Observed rules (approximate): if holiday falls on weekend, often observed on nearest weekday — not always
        // We'll add a conservative observed-on-Monday rule for some fixed holidays (New Year and Christmas)
        // Keep simple: if holiday falls on Sunday, set observed on next Monday with suffix ' (Observed)'
        $additional = [];
        foreach ($holidays as $ymd => $name) {
            try {
                $d = Carbon::parse($ymd);
            } catch (\Exception $e) { continue; }
            if ($d->isSunday()) {
                $obs = $d->copy()->addDay();
                // only add observed if not already a holiday there
                if (!isset($holidays[$obs->toDateString()])) {
                    $additional[$obs->toDateString()] = $name . ' (Observed)';
                }
            }
        }
        foreach ($additional as $k => $v) $holidays[$k] = $v;

        // Ensure keys sorted chronologically
        ksort($holidays);
        return $holidays;
    }

    /**
     * Compute the Easter date for the given year using Anonymous Gregorian algorithm.
     * Returns array [Y, M, D]
     * @param int $y
     * @return array<int,int,int>
     */
    protected static function easterDate(int $y): array
    {
        // Anonymous Gregorian algorithm
        $a = $y % 19;
        $b = intdiv($y, 100);
        $c = $y % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv(($b + 8), 25);
        $g = intdiv(($b - $f + 1), 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv(($a + 11 * $h + 22 * $l), 451);
        $month = intdiv(($h + $l - 7 * $m + 114), 31);
        $day = (($h + $l - 7 * $m + 114) % 31) + 1;
        return [$y, $month, $day];
    }
}
