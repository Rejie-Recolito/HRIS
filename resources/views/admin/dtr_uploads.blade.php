@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white dark:text-gray-200 leading-tight flex items-center">
        <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2" />
        </svg>
        DTR Uploads
    </h2>
@endsection

@section('content')
<div class="container mx-auto p-4">
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
@endsection
