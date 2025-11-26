@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white dark:text-gray-200 leading-tight flex items-center">
        <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2" />
        </svg>
        {{ __('DAILY TIME RECORD') }}
    </h2>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                        {{ session('error') }}
                        @if(isset($upload) && $upload->status === 'not stored')
                            <div class="flex gap-4 mt-4">
                                <form method="POST" action="{{ route('admin.dtr.store', ['upload' => $upload->id]) }}">
                                    @csrf
                                    <button type="submit" class="bg-[#198f51] hover:bg-[#166c3c] text-white font-bold py-2 px-4 rounded-lg">Save</button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endif

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif



                @if(isset($uploads) && count($uploads))
                <div class="mt-8">
                    <h3 class="text-lg text-[#198f51] font-bold mb-2">DTR UPLOADS</h3>
                    <table class="admin-table min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-2 py-1">Date</th>
                                <th class="px-2 py-1">Filename</th>
                                <th class="px-2 py-1">Status</th>
                                <th class="px-2 py-1">Uploaded At</th>
                                <th class="px-2 py-1">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($uploads as $upload)
                            <tr>
                                <td class="px-2 py-1">
                                    @php
                                        $firstEntry = $upload->entries()->orderBy('occurred_at')->first();
                                    @endphp
                                    @if($firstEntry && $firstEntry->occurred_at)
                                        {{ \Carbon\Carbon::parse($firstEntry->occurred_at)->format('Y F') }}
                                    @else
                                        <span class="text-gray-400 italic">No Date</span>
                                    @endif
                                </td>
                                <td class="px-2 py-1">{{ $upload->filename }}</td>
                                <td class="px-2 py-1">
                                    @if($upload->status === 'not stored')
                                        Not Stored
                                    @elseif($upload->status === 'stored')
                                        Stored
                                    @else
                                        {{ ucfirst($upload->status) }}
                                    @endif
                                </td>
                                <td class="px-2 py-1">{{ $upload->created_at }}</td>
                                <td class="px-2 py-1 flex gap-2 items-center">
                                    <a href="{{ route('admin.dtr.uploads.view', $upload->id) }}" class="text-blue-600 underline">View</a>
                                    <form method="POST" action="{{ route('admin.dtr.uploads.delete', $upload->id) }}" onsubmit="return confirm('Are you sure you want to delete this DTR upload?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 underline ml-2">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('dtr.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label class="block text-lg font-bold mt-10 mb-2 text-[#198f51]">UPLOAD NEW RECORD</label>
                    <input type="file" name="csv" accept=".csv" class="mb-2 rounded border border-[#198f51]" />

                    <div>
                        <button type="submit" class="inline-flex items-center mt-2 px-4 py-2 bg-[#198f51] border border-transparent rounded-lg font-semibold text-white">
                            Upload and Parse
                        </button>
                    </div>
                </form>



                {{-- DTR Search and Results --}}
                <div class="mt-10">
                    @if(auth()->user()->is_admin)
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
                        @php
                            $showDtrResults = isset($dtrEntries) && request()->has('emp_id') && request()->has('month');
                        @endphp
                        <div id="dtr-results-section" @if(!$showDtrResults) style="display:none;" @endif>
                        @if($showDtrResults)
                            <div class="flex justify-end mb-2 items-center gap-3">
                                <a href="{{ route('admin.dtr.generate_pdf', ['emp_id' => request('emp_id'), 'month' => request('month')]) }}" class="inline-flex items-center px-4 py-2 bg-[#1f8f51] text-white rounded-lg">Generate PDF (HTML template)</a>
                                <a href="{{ route('admin.dtr.generate_docx_pdf', ['emp_id' => request('emp_id'), 'month' => request('month')]) }}" class="inline-flex items-center px-4 py-2 bg-[#0b62a3] text-white rounded-lg">Generate PDF (DOCX template)</a>
                                <button type="button" onclick="document.getElementById('dtr-results-section').style.display='none'" class="text-gray-500 hover:text-red-600 font-bold text-lg">&times; Close</button>
                            </div>
                            @php
                                // Get all unique headers from the raw field of all entries, preserving order from the first entry
                                $firstRaw = null;
                                foreach($dtrEntries as $entry) {
                                    if (is_array($entry->raw) && count($entry->raw)) {
                                        $firstRaw = $entry->raw;
                                        break;
                                    }
                                }
                                $allHeaders = $firstRaw ? array_keys($firstRaw) : [];
                                // Fallback: collect all unique headers if firstRaw is empty
                                if (!$allHeaders) {
                                    $allHeaders = collect($dtrEntries)
                                        ->flatMap(function($entry) { return is_array($entry->raw) ? array_keys($entry->raw) : []; })
                                        ->unique()
                                        ->filter(fn($h) => $h !== '_parsed_date')
                                        ->values()
                                        ->all();
                                }
                                // Remove _parsed_date if present
                                $allHeaders = array_filter($allHeaders, fn($h) => $h !== '_parsed_date');
                                // Set custom column widths
                                $wideCols = [
                                    'Emp Name' => 'min-w-[220px]',
                                    'Position' => 'min-w-[220px]',
                                ];
                            @endphp
                            @if(count($dtrEntries) && count($allHeaders))
                                <div class="overflow-x-auto" style="max-width:100vw;">
                                    <table class="admin-table min-w-full divide-y divide-gray-200 mt-2" style="min-width:900px;">
                                        <thead>
                                            <tr>
                                                @foreach($allHeaders as $h)
                                                    <th class="px-2 py-1 {{ $wideCols[$h] ?? '' }}">{{ strtoupper($h) }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dtrEntries as $entry)
                                                <tr>
                                                    @foreach($allHeaders as $h)
                                                        <td class="px-2 py-1 {{ $wideCols[$h] ?? '' }}">{{ $entry->raw[$h] ?? '' }}</td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @elseif(count($dtrEntries))
                                <div class="overflow-x-auto">
                                    <table class="admin-table min-w-full divide-y divide-gray-200 mt-2">
                                        <thead>
                                            <tr>
                                                <th class="px-2 py-1">Date</th>
                                                <th class="px-2 py-1">Time In</th>
                                                <th class="px-2 py-1">Time Out</th>
                                                <th class="px-2 py-1">Employee ID</th>
                                                <th class="px-2 py-1">Employee Name</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dtrEntries as $entry)
                                                <tr>
                                                    <td class="px-2 py-1">{{ $entry->occurred_at }}</td>
                                                    <td class="px-2 py-1">{{ $entry->time_in }}</td>
                                                    <td class="px-2 py-1">{{ $entry->time_out }}</td>
                                                    <td class="px-2 py-1">{{ $entry->emp_id }}</td>
                                                    <td class="px-2 py-1">{{ $entry->emp_name ?? $entry->employee ?? '' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-gray-500 mt-4">No DTR records found for this Employee ID and month.</div>
                                {{-- Debug: Show all DTR entries for this month regardless of Employee ID --}}
                                @php
                                    $debugMonth = request('month', now()->format('Y-m'));
                                    $debugEntries = \App\Models\DtrEntry::where('occurred_at', 'like', $debugMonth . '%')->orderBy('occurred_at')->get();
                                @endphp
                                @if(count($debugEntries))
                                    <div class="mt-6 p-4 border border-yellow-400 bg-yellow-50 rounded">
                                        <div class="font-bold text-yellow-700 mb-2">Debug: All DTR entries for month {{ $debugMonth }}</div>
                                        <div class="overflow-x-auto">
                                            <table class="admin-table min-w-full divide-y divide-gray-200 mt-2">
                                                <thead>
                                                    <tr>
                                                        <th class="px-2 py-1">Date</th>
                                                        <th class="px-2 py-1">Employee ID</th>
                                                        <th class="px-2 py-1">Employee Name</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($debugEntries as $entry)
                                                        <tr>
                                                            <td class="px-2 py-1">{{ $entry->occurred_at }}</td>
                                                            <td class="px-2 py-1">{{ $entry->emp_id }}</td>
                                                            <td class="px-2 py-1">{{ $entry->emp_name ?? $entry->employee ?? '' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endif
                        </div>
                    @else
                        <form method="GET" action="{{ route('employee.dtr.self') }}" class="flex flex-col md:flex-row gap-4 items-end mb-6">
                            <div>
                                <label for="month" class="block text-sm font-bold mb-1">Month</label>
                                <input type="month" name="month" id="month" value="{{ request('month', now()->format('Y-m')) }}" class="rounded border border-[#198f51] px-2 py-1" required>
                            </div>
                            <button type="submit" class="bg-[#198f51] text-white font-bold py-2 px-6 rounded-lg">View My DTR</button>
                        </form>
                        @if(isset($dtrEntries))
                            @if(count($dtrEntries))
                                <div class="overflow-x-auto">
                                    <table class="admin-table min-w-full divide-y divide-gray-200 mt-2">
                                        <thead>
                                            <tr>
                                                <th class="px-2 py-1">Date</th>
                                                <th class="px-2 py-1">Time In</th>
                                                <th class="px-2 py-1">Time Out</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dtrEntries as $entry)
                                                <tr>
                                                    <td class="px-2 py-1">{{ $entry->occurred_at }}</td>
                                                    <td class="px-2 py-1">{{ $entry->time_in }}</td>
                                                    <td class="px-2 py-1">{{ $entry->time_out }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-gray-500 mt-4">No DTR records found for this month.</div>
                            @endif
                        @endif
                    @endif
                </div
            </div>
        </div>
    </div>
@endsection