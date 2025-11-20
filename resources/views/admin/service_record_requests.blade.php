<?php /* Blade template for service record requests */ ?>
@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white dark:text-white leading-tight">Service Record Requests</h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card-bg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold">Service Record Requests</h2>
                        <div class="space-x-2">
                            <a href="{{ route('service-record-requests.index') }}" class="px-3 py-1 rounded {{ request()->routeIs('service-record-requests.index') ? 'bg-green-600 text-white' : 'bg-white text-green-600 border' }}">Pending</a>
                            <a href="{{ route('service-record-requests.history') }}" class="px-3 py-1 rounded {{ request()->routeIs('service-record-requests.history') ? 'bg-green-600 text-white' : 'bg-white text-green-600 border' }}">History</a>
                        </div>
                    </div>

                    <table class="admin-table mb-6">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Requested At</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($requests as $r)
                                <tr>
                                    <td>{{ $r->name }}</td>
                                    <td>{{ $r->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @php
                                            $status = $r->request_status ?? 'pending';
                                            $badgeClasses = [
                                                'pending' => 'bg-green-100 text-green-800 px-2 py-1 rounded text-sm font-medium',
                                                'in_progress' => 'bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm font-medium',
                                                'accepted' => 'bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm font-medium',
                                                'declined' => 'bg-red-100 text-red-800 px-2 py-1 rounded text-sm font-medium',
                                                'deleted' => 'bg-gray-100 text-gray-700 px-2 py-1 rounded text-sm font-medium',
                                            ];
                                        @endphp
                                        <span class="{{ $badgeClasses[$status] ?? $badgeClasses['pending'] }}">{{ ucwords(str_replace('_', ' ', $status)) }}</span>
                                    </td>
                                    <td class="flex items-center space-x-2">
                                        @if($r->request_status === 'pending')
                                            <form method="POST" action="{{ route('service-record-requests.accept', ['id' => $r->id]) }}" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">Accept</button>
                                            </form>
                                        @elseif($r->request_status === 'in_progress' && $r->service_record_id)
                                            <a href="{{ route('service-records.edit', ['id' => $r->service_record_id]) }}" class="bg-yellow-500 text-white px-3 py-1 rounded">Resume</a>
                                        @endif

                                        <form method="POST" action="{{ route('service-record-requests.destroy', ['id' => $r->id]) }}" style="display:inline-block;" onsubmit="return confirm('Delete this request?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
