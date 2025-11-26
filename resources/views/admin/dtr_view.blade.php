@extends('layouts.app')

@section('header')
    @php
        $dtrDate = null;
        if(count($entries) > 0) {
            // Prefer occurred_at if available
            $dtrDate = $entries[0]->occurred_at;
            // If not, try to get from first column of raw
            if(!$dtrDate && isset($entries[0]->raw) && is_array($entries[0]->raw)) {
                $firstRaw = $entries[0]->raw;
                $firstKey = array_key_first($firstRaw);
                $dtrDate = $firstRaw[$firstKey] ?? null;
            }
        }
    @endphp
    <h2 class="font-semibold text-xl text-white dark:text-gray-200 leading-tight flex items-center">
        <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2" />
        </svg>
        DTR for {{ $dtrDate ? (\Carbon\Carbon::parse($dtrDate)->format('Y F d')) : 'Unknown Date' }}
    </h2>
@endsection

@section('content')
<div class="container mx-auto p-4">
    {{-- DTR Search Form (Admin) --}}
    @if(auth()->user() && auth()->user()->is_admin)
        <form method="GET" action="{{ route('admin.dtr.search') }}" class="flex flex-col md:flex-row gap-4 items-end mb-6">
            <div>
                <label for="emp_id" class="block text-sm font-bold mb-1">Employee ID</label>
                <input type="text" name="emp_id" id="emp_id" value="{{ request('emp_id') }}" class="rounded border border-[#198f51] px-2 py-1" placeholder="Enter Employee ID" required>
            </div>
            <div>
                <label for="month" class="block text-sm font-bold mb-1">Month</label>
                <input type="month" name="month" id="month" value="{{ request('month', now()->format('Y-m')) }}" class="rounded border border-[#198f51] px-2 py-1" required>
            </div>
            <button type="submit" class="bg-[#198f51] text-white font-bold py-2 px-6 rounded-lg">Search</button>
        </form>
    @endif
    {{-- Back to Uploads link removed as requested --}}
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
    @endif

    {{-- Always show Save button for not stored uploads --}}
    @if(isset($upload) && $upload->status === 'not stored')
        <div class="flex gap-4 mt-4">
            <form method="POST" action="{{ route('admin.dtr.store', ['upload' => $upload->id]) }}">
                @csrf
                <button type="submit" class="bg-[#198f51] hover:bg-[#166c3c] text-white font-bold py-2 px-4 rounded-lg">Save</button>
            </form>
        </div>
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
