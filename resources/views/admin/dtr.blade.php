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
                                        {{ \Carbon\Carbon::parse($firstEntry->occurred_at)->format('Y F d') }}
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


                @if(isset($groupedRecords))
                    <div class="mt-6">
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
                                        <button type="submit" class="bg-[#198f51] text-white font-bold py-2 px-9 rounded-lg">STORE</button>
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
                                @foreach($months as $month => $days)
                                    @foreach($days as $day => $records)
                                        <div class="mt-4">
                                            <div class="flex items-center gap-4 mb-1">
                                                <span class="text-lg font-semibold">{{ $year }}</span>
                                                <span class="font-medium">{{ $month }}</span>
                                                <span class="font-medium">Day {{ $day }}</span>
                                            </div>
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
                        @if(isset($upload) && $upload->status === 'not stored')
                            <div class="flex gap-4 mt-4">
                                <form method="POST" action="{{ route('admin.dtr.store', ['upload' => $upload->id]) }}">
                                    @csrf
                                    <button type="submit" class="bg-[#198F51] text-white font-bold py-2 px-8 rounded-lg">Store</button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection