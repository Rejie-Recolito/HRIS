@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">DTR Entries for Upload #{{ $upload->id }}</h1>
    <a href="{{ route('admin.dtr.uploads') }}" class="text-blue-600 underline mb-4 inline-block">&larr; Back to Uploads</a>
    @php
        $hasEntries = count($entries) > 0;
        $headers = $hasEntries && is_array($entries[0]->raw) ? array_keys($entries[0]->raw) : [];
        // Remove _parsed_date column if present
        $headers = array_filter($headers, fn($h) => $h !== '_parsed_date');
        // Set column widths similar to preview
        $wideCols = [
            'Date' => 'min-w-[140px]',
            'EMP ID' => 'min-w-[100px]',
            'EMP NAME' => 'min-w-[180px]',
            'DEPARTMENT' => 'min-w-[240px]',
            'POSITION' => 'min-w-[180px]',
            'AM-ARRIVAL' => 'min-w-[120px]',
            'AM-DEPARTURE' => 'min-w-[120px]',
            'UNDERTIME-HOURS' => 'min-w-[120px]',
            'UNDERTIME-MINUTES' => 'min-w-[120px]',
        ];
    @endphp
    @if(!$hasEntries)
        <div class="text-gray-600 italic">No DTR entries found for this upload.</div>
    @elseif(count($headers))
        <div class="overflow-x-auto">
        <table class="admin-table min-w-full divide-y divide-gray-200 mt-2">
            <thead>
                <tr>
                    @foreach($headers as $h)
                        <th class="px-2 py-1 text-left text-md font-medium text-white {{ $wideCols[strtoupper($h)] ?? '' }}">{{ strtoupper($h) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($entries as $entry)
                    <tr>
                        @foreach($headers as $h)
                            <td class="px-2 py-1 text-sm {{ $wideCols[strtoupper($h)] ?? '' }}">{{ $entry->raw[$h] ?? '' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    @else
        <table class="admin-table min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-2 py-1">Date</th>
                    <th class="px-2 py-1">Employee</th>
                    <th class="px-2 py-1">Time In</th>
                    <th class="px-2 py-1">Time Out</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entries as $entry)
                <tr>
                    <td class="px-2 py-1">{{ $entry->occurred_at }}</td>
                    <td class="px-2 py-1">{{ $entry->employee }}</td>
                    <td class="px-2 py-1">{{ $entry->time_in }}</td>
                    <td class="px-2 py-1">{{ $entry->time_out }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
