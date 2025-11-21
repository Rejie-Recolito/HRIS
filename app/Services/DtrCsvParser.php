<?php

namespace App\Services;

use Carbon\Carbon;

class DtrCsvParser
{
    /**
     * Parse a CSV file path and return headers, rows, and groupedRecords.
     * Supports optional explicit date column name and optional date format string.
     * Uses a streaming approach (fgetcsv) to avoid loading entire file into memory.
     *
     * @param string $filePath
     * @param string|null $dateColumn
     * @param string|null $dateFormat
     * @return array
     */
    public function parseUploadedFile(string $filePath, ?string $dateColumn = null, ?string $dateFormat = null): array
    {
        $stream = fopen($filePath, 'r');
        if (!$stream) {
            throw new \RuntimeException('Unable to open file');
        }

        $headers = [];
        $rows = [];

        // Read header
        if (($data = fgetcsv($stream)) !== false) {
            $headers = $data;
        }

        // If user provided a date column but headers exist, validate it exists
        if ($dateColumn && !empty($headers) && !in_array($dateColumn, $headers, true)) {
            // allow numeric index as column
            if (!is_numeric($dateColumn)) {
                // ignore and fallback to detection
                $dateColumn = null;
            }
        }

        while (($data = fgetcsv($stream)) !== false) {
            if (count($data) === 1 && trim($data[0]) === '') {
                continue;
            }

            if (count($headers) && count($data) < count($headers)) {
                $data = array_pad($data, count($headers), '');
            }

            $row = count($headers) ? array_combine($headers, $data) : $data;

            $rows[] = $row;
        }

        fclose($stream);

        // Auto-detect date column if not provided
        $detectedDateColumn = $dateColumn;
        if (!$detectedDateColumn && !empty($headers)) {
            foreach ($headers as $h) {
                if (preg_match('/date|day|time/i', $h)) {
                    $detectedDateColumn = $h;
                    break;
                }
            }

            // fallback to first column
            if (!$detectedDateColumn) {
                $detectedDateColumn = $headers[0] ?? null;
            }
        }

        // Parse dates and attach _parsed_date
        foreach ($rows as &$row) {
            $row['_parsed_date'] = null;
            $value = null;
            if (is_array($row)) {
                if ($detectedDateColumn !== null && array_key_exists($detectedDateColumn, $row)) {
                    $value = $row[$detectedDateColumn];
                } else {
                    $value = $row[0] ?? null;
                }
            } else {
                $value = $row;
            }

            if ($value !== null && $value !== '') {
                $trimmed = trim((string)$value);
                // Try user-supplied format first
                if ($dateFormat) {
                    try {
                        $row['_parsed_date'] = Carbon::createFromFormat($dateFormat, $trimmed);
                    } catch (\Exception $e) {
                        $row['_parsed_date'] = null;
                    }
                }

                if (!$row['_parsed_date']) {
                    // Try flexible parse with Carbon
                    try {
                        $row['_parsed_date'] = Carbon::parse($trimmed);
                    } catch (\Exception $e) {
                        $row['_parsed_date'] = null;
                    }
                }
            }
        }
        unset($row);

        // Sort by parsed date
        usort($rows, function ($a, $b) {
            $ad = $a['_parsed_date'] ?? null;
            $bd = $b['_parsed_date'] ?? null;
            if ($ad && $bd) {
                return $ad->lt($bd) ? -1 : ($ad->gt($bd) ? 1 : 0);
            }
            if ($ad) return -1;
            if ($bd) return 1;
            return 0;
        });

        // Group rows
        $grouped = [];
        foreach ($rows as $r) {
            $dt = $r['_parsed_date'] ?? null;
            if (!$dt) {
                $grouped['ungrouped'][] = $r;
                continue;
            }
            $y = $dt->year;
            $m = $dt->format('F');
            $d = $dt->day;
            $grouped[$y][$m][$d][] = $r;
        }

        return [
            'headers' => $headers,
            'rows' => $rows,
            'groupedRecords' => $grouped,
            'detected_date_column' => $detectedDateColumn,
        ];
    }
}
