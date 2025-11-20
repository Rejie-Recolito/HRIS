@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white dark:text-white leading-tight flex items-center">
        <svg class="w-8 h-8 mr-3 header-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        LEAVE APPLICATION HISTORY
    </h2>
@endsection

@section('content')
<style>
    .header-icon {
        display: inline-block;
        vertical-align: middle;
        margin-top: 2px;
        filter: brightness(0) invert(1);
    }
    
    .status-badge {
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-align: center;
        white-space: nowrap;
    }
    
    .status-submitted {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .status-under-review {
        background-color: #dbeafe;
        color: #1e40af;
    }
    
    .status-approved {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .status-denied {
        background-color: #fee2e2;
        color: #991b1b;
    }
    
    .dark .status-submitted {
        background-color: #78350f;
        color: #fef3c7;
    }
    
    .dark .status-under-review {
        background-color: #1e3a8a;
        color: #dbeafe;
    }
    
    .dark .status-approved {
        background-color: #064e3b;
        color: #d1fae5;
    }
    
    .dark .status-denied {
        background-color: #7f1d1d;
        color: #fee2e2;
    }
</style>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-[#282828] overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <!-- Search and Filter Form -->
                <form method="GET" action="{{ route('leave.history') }}" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search by Name -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Search Employee
                            </label>
                            <input 
                                type="text" 
                                name="search" 
                                id="search" 
                                value="{{ request('search') }}"
                                placeholder="Enter name..." 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-[#198f51] focus:border-[#198f51] dark:bg-gray-700 dark:text-white"
                            >
                        </div>

                        <!-- Filter by Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Status
                            </label>
                            <select 
                                name="status" 
                                id="status"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-[#198f51] focus:border-[#198f51] dark:bg-gray-700 dark:text-white"
                            >
                                <option value="">All Statuses</option>
                                <option value="Submitted" {{ request('status') === 'Submitted' ? 'selected' : '' }}>Submitted</option>
                                <option value="Under Review" {{ request('status') === 'Under Review' ? 'selected' : '' }}>Under Review</option>
                                <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved</option>
                                <option value="Denied" {{ request('status') === 'Denied' ? 'selected' : '' }}>Denied</option>
                            </select>
                        </div>

                        <!-- Filter by Leave Type -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Leave Type
                            </label>
                            <select 
                                name="type" 
                                id="type"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-[#198f51] focus:border-[#198f51] dark:bg-gray-700 dark:text-white"
                            >
                                <option value="">All Types</option>
                                <option value="Vacation leave" {{ request('type') === 'Vacation leave' ? 'selected' : '' }}>Vacation Leave</option>
                                <option value="Sick leave" {{ request('type') === 'Sick leave' ? 'selected' : '' }}>Sick Leave</option>
                                <option value="Mandatory/Forced leave" {{ request('type') === 'Mandatory/Forced leave' ? 'selected' : '' }}>Mandatory/Forced Leave</option>
                                <option value="Maternity leave" {{ request('type') === 'Maternity leave' ? 'selected' : '' }}>Maternity Leave</option>
                                <option value="Paternity leave" {{ request('type') === 'Paternity leave' ? 'selected' : '' }}>Paternity Leave</option>
                                <option value="Special Privilege Leave" {{ request('type') === 'Special Privilege Leave' ? 'selected' : '' }}>Special Privilege Leave</option>
                                <option value="Solo Parent leave" {{ request('type') === 'Solo Parent leave' ? 'selected' : '' }}>Solo Parent Leave</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-end gap-2">
                            <button 
                                type="submit" 
                                class="flex-1 bg-[#198f51] text-white px-4 py-2 rounded-md hover:bg-[#156b3f] transition-colors"
                            >
                                Filter
                            </button>
                            <a 
                                href="{{ route('leave.history') }}" 
                                class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors text-center"
                            >
                                Clear
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Results Count -->
                <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    Showing {{ $applications->firstItem() ?? 0 }} to {{ $applications->lastItem() ?? 0 }} of {{ $applications->total() }} applications
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 20%; border-color: #ffffff;">Employee Name</th>
                                <th style="width: 15%; border-color: #ffffff;">Leave Type</th>
                                <th style="width: 10%; border-color: #ffffff;">Date Filed</th>
                                <th style="width: 10%; border-color: #ffffff;">No. of Days</th>
                                <th style="width: 15%; border-color: #ffffff;">Inclusive Dates</th>
                                <th style="width: 12%; border-color: #ffffff;">Status</th>
                                <th style="width: 10%; border-color: #ffffff;">Approved Date</th>
                                <th style="width: 8%; border-color: #ffffff;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $application)
                                <tr>
                                    <td>{{ $application->lastname }}, {{ $application->firstname }} {{ $application->middlename }}</td>
                                    <td>{{ $application->type_of_leave }}</td>
                                    <td>{{ \Carbon\Carbon::parse($application->date_of_filing)->format('M d, Y') }}</td>
                                    <td class="text-center">{{ $application->number_of_days }}</td>
                                    <td>
                                        @if($application->status === 'Approved' && $application->inclusive_dates)
                                            {{ $application->inclusive_dates }}
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $application->status)) }}">
                                            {{ $application->status }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($application->approved_at)
                                            {{ $application->approved_at->format('M d, Y') }}
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a 
                                            href="{{ route('leave_application.view', $application->id) }}" 
                                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                            title="View Details"
                                        >
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-8 text-gray-500 dark:text-gray-400">
                                        No leave applications found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $applications->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
