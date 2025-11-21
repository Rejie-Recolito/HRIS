<?php

use App\Services\DtrCsvParser;
use Illuminate\Support\Facades\Storage;

it('parses sample dtr csv and groups by date', function () {
    $parser = new DtrCsvParser();
    $fixture = base_path('tests/Fixtures/dtr_sample.csv');
    expect(file_exists($fixture))->toBeTrue();

    $result = $parser->parseUploadedFile($fixture);

    // headers present
    expect($result['headers'])->toBeArray();
    expect($result['rows'])->toBeArray();

    // groupedRecords should have years and ungrouped
    expect(array_key_exists('ungrouped', $result['groupedRecords']))->toBeTrue();

    // Ensure that bad-date row ended up in ungrouped
    $ungrouped = $result['groupedRecords']['ungrouped'];
    $foundBad = false;
    foreach ($ungrouped as $r) {
        if (is_array($r) && isset($r['Date']) && $r['Date'] === 'bad-date') {
            $foundBad = true;
            break;
        }
    }
    expect($foundBad)->toBeTrue();

    // Ensure parsed entries are grouped under 2025
    expect(array_key_exists(2025, $result['groupedRecords']))->toBeTrue();
});
