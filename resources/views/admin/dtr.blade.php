@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Daily Time Record') }}
    </h2>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <p class="mb-4">{{ __("This is the Daily Time Record page") }}</p>

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('dtr.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label class="block mb-2">Upload DTR CSV</label>
                    <input type="file" name="csv" accept=".csv" class="mb-2" />

                    @if(!empty($headers))
                        <div class="mb-2">
                            <label class="block text-sm">Date column</label>
                            <select name="date_column" class="mt-1 block w-full">
                                <option value="">(auto-detect)</option>
                                @foreach($headers as $h)
                                    <option value="{{ $h }}" {{ (old('date_column') == $h || (isset($detected_date_column) && $detected_date_column == $h)) ? 'selected' : '' }}>{{ $h }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="mb-2">
                        <label class="block text-sm">Optional date format (PHP date format for parse)</label>
                        <input type="text" name="date_format" value="{{ old('date_format') ?? ($date_format ?? '') }}" class="mt-1 block w-full" placeholder="e.g. Y-m-d H:i:s or m/d/Y" />
                    </div>

                    <div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white">
                            Upload and Parse
                        </button>
                    </div>
                </form>

                @if(isset($upload))
                    <div class="mt-6 p-4 border rounded bg-gray-50 dark:bg-gray-900">
                        <p class="font-medium">Upload: {{ $upload->filename }}</p>
                        <p>Status: <span class="font-semibold">{{ $upload->status }}</span></p>
                        @if(isset($upload->path))
                            <p class="mt-2">
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($upload->path) }}" class="text-blue-600 underline">Download raw CSV</a>
                            </p>
                        @endif
                    </div>
                @endif

                @if(isset($groupedRecords))
                    <div class="mt-6">
                        <h3 class="font-semibold">Parsed Records</h3>

                        @if(!empty($groupedRecords['ungrouped'] ?? null))
                            <div class="mt-4">
                                <h4 class="font-medium">Ungrouped / Unparsed Dates</h4>
                                <table class="min-w-full divide-y divide-gray-200 mt-2">
                                    <thead>
                                        <tr>
                                            @if(!empty($headers))
                                                @foreach($headers as $h)
                                                    <th class="px-2 py-1 text-left text-xs font-medium text-gray-500">{{ $h }}</th>
                                                @endforeach
                                            @else
                                                <th class="px-2 py-1">Value</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($groupedRecords['ungrouped'] as $r)
                                            <tr>
                                                @if(is_array($r))
                                                    @foreach($r as $c)
                                                        <td class="px-2 py-1 text-sm">{{ $c }}</td>
                                                    @endforeach
                                                @else
                                                    <td class="px-2 py-1">{{ $r }}</td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        @foreach($groupedRecords as $year => $months)
                            @if($year === 'ungrouped')
                                @continue
                            @endif
                            <div class="mt-6 border-t pt-4">
                                <h4 class="text-lg font-semibold">{{ $year }}</h4>
                                @foreach($months as $month => $days)
                                    <div class="mt-3">
                                        <h5 class="font-medium">{{ $month }}</h5>
                                        @foreach($days as $day => $records)
                                            <div class="mt-2 pl-4">
                                                <h6 class="font-medium">Day {{ $day }}</h6>
                                                <table class="min-w-full divide-y divide-gray-200 mt-1">
                                                    <thead>
                                                        <tr>
                                                            @if(!empty($headers))
                                                                @foreach($headers as $h)
                                                                    <th class="px-2 py-1 text-left text-xs font-medium text-gray-500">{{ $h }}</th>
                                                                @endforeach
                                                            @else
                                                                <th class="px-2 py-1">Value</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($records as $r)
                                                            <tr>
                                                                @if(is_array($r))
                                                                    @foreach($r as $k => $c)
                                                                        @if($k === '_parsed_date')
                                                                            <td class="px-2 py-1 text-sm">{{ $c ? $c->toDateTimeString() : '' }}</td>
                                                                        @else
                                                                            <td class="px-2 py-1 text-sm">{{ $c }}</td>
                                                                        @endif
                                                                    @endforeach
                                                                @else
                                                                    <td class="px-2 py-1">{{ $r }}</td>
                                                                @endif
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection