@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white dark:text-white leading-tight flex items-center">
        <img src="{{ asset('Images/leave-icon.svg') }}" class="w-8 h-8 mr-3 header-icon" alt="Leave Icon">
        LEAVE APPLICATION
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Leave Applications Table -->
            <div class="card-bg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @livewire('leave-applications-table')
                </div>
            </div>

            <!-- Leave History Section -->
            <div class="bg-white dark:bg-[#282828] overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-6 text-gray-900 dark:text-white">Leave Application History</h3>
                    
                    <!-- Search and Filter Form -->
                    <form method="GET" action="{{ route('leave') }}" class="mb-6">
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
                                    href="{{ route('leave') }}" 
                                    class="flex-1 bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors text-center"
                                >
                                    Clear
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Results Count -->
                    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                        Showing {{ $historyApplications->firstItem() ?? 0 }} to {{ $historyApplications->lastItem() ?? 0 }} of {{ $historyApplications->total() }} applications
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th style="width: 20%; border-color: #ffffff;">Employee Name</th>
                                    <th style="width: 15%; border-color: #ffffff;">Leave Type</th>
                                    <th style="width: 10%; border-color: #ffffff;">Date Filed</th>
                                    <th style="width: 6%; border-color: #ffffff; text-align: center;">No. of Days</th>
                                    <th style="width: 23%; border-color: #ffffff;">Inclusive Dates</th>
                                    <th style="width: 10%; border-color: #ffffff; text-align: center;">Status</th>
                                    <th style="width: 12%; border-color: #ffffff;">Action Date</th>
                                    <th style="width: 4%; border-color: #ffffff; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historyApplications as $application)
                                    <tr>
                                        <td>{{ $application->lastname }}, {{ $application->firstname }} {{ $application->middlename }}</td>
                                        <td>{{ $application->type_of_leave }}</td>
                                        <td class="text-sm">{{ \Carbon\Carbon::parse($application->date_of_filing)->format('M d, Y') }}</td>
                                        <td class="text-center">{{ $application->number_of_days }}</td>
                                        <td class="text-sm">
                                            @if($application->status === 'Approved' && $application->inclusive_dates)
                                                {{ $application->inclusive_dates }}
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $application->status)) }}">
                                                {{ $application->status }}
                                            </span>
                                        </td>
                                        <td class="text-sm">
                                            @if($application->action_date)
                                                {{ $application->action_date->format('M d, Y') }}
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="inline-flex items-center justify-center space-x-2">
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
                                                <form id="delete-form-{{ $application->id }}" action="{{ route('leave.delete', $application->id) }}" method="POST" style="display:inline; margin:0;" class="delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="permanent" value="1" />
                                                    <button type="button" data-form-id="{{ $application->id }}" data-target-form-id="delete-form-{{ $application->id }}" class="delete-trigger inline-flex items-center justify-center px-2 py-1 rounded-md text-red-600 hover:text-red-800 focus:outline-none" title="Delete">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m4 0H5" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
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
                        {{ $historyApplications->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete confirmation modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-lg w-full p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Confirm delete</h3>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">Are you sure you want to permanently delete this leave application? This action cannot be undone.</p>
            <div class="flex justify-end gap-3">
                <button id="cancelDelete" class="px-4 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200">Cancel</button>
                <button id="confirmDelete" class="px-4 py-2 rounded-md bg-red-600 text-white">Delete</button>
            </div>
        </div>
    </div>

    <script>
        (function(){
            let modal = document.getElementById('deleteModal');
            let confirmBtn = document.getElementById('confirmDelete');
            let cancelBtn = document.getElementById('cancelDelete');
            let targetForm = null;

            document.querySelectorAll('.delete-trigger').forEach(function(btn){
                btn.addEventListener('click', function(e){
                    // prefer explicit data-target-form-id attribute if present
                    const targetId = btn.getAttribute('data-target-form-id');
                    if (targetId) {
                        targetForm = document.getElementById(targetId);
                    }
                    // fallback to closest form
                    if (!targetForm) targetForm = btn.closest('form');
                    console.log('Delete triggered for form:', targetForm ? targetForm.id : null, 'action=', targetForm ? targetForm.action : null);
                    if(!targetForm) return;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                });
            });

            cancelBtn.addEventListener('click', function(){
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                targetForm = null;
            });

            confirmBtn.addEventListener('click', function(){
                console.log('Confirm delete clicked. Performing AJAX delete for form:', targetForm ? targetForm.id : null);
                if(!targetForm) return;

                // Build FormData from the form (includes _token and _method)
                const formData = new FormData(targetForm);

                // Add header to indicate AJAX
                const headers = {
                    'X-Requested-With': 'XMLHttpRequest'
                };

                // Disable confirm button while request in progress
                confirmBtn.disabled = true;
                confirmBtn.classList.add('opacity-75', 'cursor-not-allowed');

                fetch(targetForm.action, {
                    method: 'POST',
                    headers: headers,
                    body: formData,
                    credentials: 'same-origin'
                }).then(function(resp){
                    confirmBtn.disabled = false;
                    confirmBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                    if (!resp.ok) throw new Error('Network response was not ok: ' + resp.status);
                    return resp.json();
                }).then(function(json){
                    console.log('Delete response:', json);
                    if (json && json.success) {
                        // remove the table row containing the form
                        const row = targetForm.closest('tr');
                        if (row) row.remove();
                        // optionally show a toast / flash — here we log
                        console.info(json.message || 'Deleted');
                    } else {
                        console.warn('Delete responded with success=false', json);
                    }
                }).catch(function(err){
                    console.error('AJAX delete failed', err);
                }).finally(function(){
                    // hide modal and reset
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    targetForm = null;
                });
            });
        })();
    </script>
@endsection
