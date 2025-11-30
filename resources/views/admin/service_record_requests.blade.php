<?php /* Blade template for service record requests */ ?>
@extends('layouts.app')


@section('header')
    <h2 class="font-semibold text-xl text-white dark:text-white leading-tight flex items-center whitespace-nowrap">
        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
        </svg>
        SERVICE RECORD REQUESTS
    </h2>
@endsection

@section('content')
    <div>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card-bg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-[#198f51] text-xl font-bold">REQUESTS</h2>
                        <div class="space-x-2">
                            <a href="{{ route('service-record-requests.index') }}" class="px-3 py-1 rounded {{ request()->routeIs('service-record-requests.index') ? 'bg-green-600 text-white' : 'bg-white text-green-600 border' }}">Recent</a>
                            <a href="{{ route('service-record-requests.history') }}" class="px-3 py-1 rounded {{ request()->routeIs('service-record-requests.history') ? 'bg-green-600 text-white' : 'bg-white text-green-600 border' }}">History</a>
                        </div>
                    </div>

                    <table class="admin-table mb-6">
                        <thead>
                            <tr>
                                <th>EMPLOYEE</th>
                                <th>DATE OF REQUEST</th>
                                <th>STATUS</th>
                                <th>ACTIONS</th>
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
                                        @if($r->request_status === 'pending' || $r->request_status === 'in_progress')
                                            <a href="{{ route('service-record-requests.process', ['id' => $r->id]) }}" class="bg-blue-600 text-white px-3 py-1 rounded">Process</a>
                                        @endif

                                        <form method="POST" action="{{ route('service-record-requests.destroy', ['id' => $r->id]) }}" style="display:inline-block;" onsubmit="return confirm('Delete this request?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" aria-label="Delete request" title="Delete Request" class="inline-flex items-center justify-center w-8 h-8 rounded hover:bg-red-100 text-red-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m4 0H5" />
                                                </svg>
                                            </button>
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
