@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white dark:text-gray-200 leading-tight">
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
                        @if(isset($upload) && $upload->status === 'Not Stored')
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


                <div class="mb-6">
                    
                    <a href="{{ route('admin.dtr.uploads') }}" class="text-[#198f51] text-lg underline">VIEW ALL RECORDS</a>
                </div>

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
                    <label class="block mb-2 text-[#198f51]">Upload new DTR csv</label>
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
                        <input type="text" name="date_format" value="{{ old('date_format') ?? ($date_format ?? '') }}" class="mt-1 block w-48" placeholder="e.g. YYYY-MM-DD" />
                    </div>

                    <div>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#198f51] border border-transparent rounded-lg font-semibold text-white">
                            Upload and Parse
                        </button>
                    </div>
                </form>

                @if(isset($upload))
                    <div class="mt-6 p-4 border rounded bg-gray-50 dark:bg-gray-900">
                        <p class="font-medium">Upload: {{ $upload->filename }}</p>
                        <p>Status: <span class="font-semibold">{{ $upload->status }}</span></p>
                    </div>
                @endif

                @if(isset($groupedRecords))
                    <div class="mt-6">
                        <h3 class="font-semibold">Parsed Records</h3>

                        @if(!empty($groupedRecords['ungrouped'] ?? null))
                            <div class="mt-4" x-data="{ show: true }" x-show="show">
                                <h4 class="font-medium">Ungrouped / Unparsed Dates</h4>
                                <div class="overflow-x-auto">
                                    <table class="admin-table min-w-full divide-y divide-gray-200 mt-2">
                                        <thead>
                                            <tr>
                                                @if(!empty($headers))
                                                    @php
                                                        $wideCols = [
                                                            'Date' => 'min-w-[140px]',
                                                            'Emp ID' => 'min-w-[100px]',
                                                            'Emp Name' => 'min-w-[180px]',
                                                            'Department' => 'min-w-[240px]',
                                                            'Position' => 'min-w-[180px]',
                                                            'Undertime-Hours' => 'min-w-[120px]',
                                                            'Undertime-Minutes' => 'min-w-[120px]',
                                                        ];
                                                    @endphp
                                                    @foreach($headers as $h)
                                                        <th class="px-2 py-1 text-left text-md font-medium text-white {{ $wideCols[$h] ?? '' }}">{{ strtoupper($h) }}</th>
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
                                                        @for($i = 0; $i < count($headers); $i++)
                                                            <td class="px-2 py-1 text-sm {{ $wideCols[$headers[$i] ?? ''] ?? '' }}">{{ $r[$i] ?? '' }}</td>
                                                        @endfor
                                                    @else
                                                        <td class="px-2 py-1">{{ $r }}</td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="flex gap-4 mt-4">
                                    <form method="POST" action="{{ route('admin.dtr.store', ['upload' => $upload->id]) }}">
                                        @csrf
                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Save</button>
                                    </form>
                                    <button type="button" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded" @click="show = false">Close</button>
                                </div>
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
                                                <div class="overflow-x-auto">
                                                    <table class="admin-table min-w-full divide-y divide-gray-200 mt-1">
                                                    <thead>
                                                        <tr>
                                                            @if(!empty($headers))
                                                                @php
                                                                    $wideCols = [
                                                                        'Date' => 'min-w-[140px]',
                                                                        'Emp ID' => 'min-w-[100px]',
                                                                        'Emp Name' => 'min-w-[180px]',
                                                                        'Department' => 'min-w-[240px]',
                                                                        'Position' => 'min-w-[160px]',
                                                                        'Undertime-Hours' => 'min-w-[120px]',
                                                                        'Undertime-Minutes' => 'min-w-[120px]',
                                                                    ];
                                                                @endphp
                                                                    @foreach($headers as $h)
                                                                        <th class="px-2 py-1 text-left text-lg font-medium text-white {{ $wideCols[$h] ?? '' }}">{{ strtoupper($h) }}</th>
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
                                                                    @for($i = 0; $i < count($headers); $i++)
                                                                        @php $c = $r[$i] ?? ($r[$headers[$i]] ?? ''); @endphp
                                                                        <td class="px-2 py-1 text-sm {{ $wideCols[$headers[$i] ?? ''] ?? '' }}">{{ $c }}</td>
                                                                    @endfor
                                                                @else
                                                                    <td class="px-2 py-1">{{ $r }}</td>
                                                                @endif
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                        @if(isset($upload) && $upload->status === 'pending')
                            <div class="flex gap-4 mt-4">
                                <form method="POST" action="{{ route('admin.dtr.store', ['upload' => $upload->id]) }}">
                                    @csrf
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Save</button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection