@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white dark:text-white leading-tight">Service Record Requests History</h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card-bg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold">Request History</h2>
                        <div class="space-x-2">
                            <a href="{{ route('service-record-requests.index') }}" class="px-3 py-1 rounded bg-white text-green-600 border">Pending</a>
                            <a href="{{ route('service-record-requests.history') }}" class="px-3 py-1 rounded bg-green-600 text-white">History</a>
                        </div>
                    </div>

                    <table class="admin-table mb-6">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Updated At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($requests as $r)
                                <tr>
                                    <td>{{ $r->name }}</td>
                                    <td>
                                        @php
                                            $status = $r->request_status ?? 'accepted';
                                            $badgeClasses = [
                                                'pending' => 'bg-green-100 text-green-800 px-2 py-1 rounded text-sm font-medium',
                                                'in_progress' => 'bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm font-medium',
                                                'accepted' => 'bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm font-medium',
                                                'declined' => 'bg-red-100 text-red-800 px-2 py-1 rounded text-sm font-medium',
                                                'deleted' => 'bg-gray-100 text-gray-700 px-2 py-1 rounded text-sm font-medium',
                                            ];
                                        @endphp
                                        <span class="{{ $badgeClasses[$status] ?? $badgeClasses['accepted'] }}">{{ ucwords(str_replace('_', ' ', $status)) }}</span>
                                    </td>
                                    <td>{{ $r->updated_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @if($r->service_record_id)
                                            <a href="{{ route('service-records.edit', ['id' => $r->service_record_id]) }}" class="bg-blue-600 text-white px-3 py-1 rounded">Open</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No history found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
