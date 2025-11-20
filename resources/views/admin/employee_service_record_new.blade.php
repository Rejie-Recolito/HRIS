@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
        <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        SERVICE RECORD - {{ strtoupper($employee->lastname) }}, {{ strtoupper($employee->firstname) }} {{ strtoupper($employee->middlename) }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="w-[98%] mx-auto px-1">
        
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded dark:bg-green-900 dark:border-green-700 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded dark:bg-red-900 dark:border-red-700 dark:text-red-200">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Employee Info Summary -->
        <div class="bg-white dark:bg-[#282828] overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4 text-[#198f51]">EMPLOYEE INFORMATION</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700 dark:text-gray-300">Department:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ $employee->department }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700 dark:text-gray-300">Job Title:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ $employee->job_title }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700 dark:text-gray-300">Status:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ $employee->status }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Records Table -->
        <div class="bg-white dark:bg-[#282828] overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-[#198f51]">SERVICE RECORDS</h3>
                    <button onclick="document.getElementById('addRecordForm').classList.remove('hidden')" 
                            class="bg-[#198f51] text-white px-4 py-2 rounded-md hover:bg-[#156b3f] transition-colors">
                        + Add New Record
                    </button>
                </div>

                <div class="overflow-x-auto" style="max-width: 100%;">
                    <table class="admin-table" style="min-width: 1700px;">
                        <thead>
                            <tr>
                                <th colspan="2" style="border-color: #ffffff; text-align: center;">SERVICE<br>(Inclusive Dates)</th>
                                <th colspan="3" style="border-color: #ffffff; text-align: center;">RECORD OF APPOINTMENT</th>
                                <th style="border-color: #ffffff; text-align: center;">OFFICE ENTITY/DIV</th>
                                <th style="border-color: #ffffff; text-align: center;">LEAVE OF ABSENCE</th>
                                <th colspan="2" style="border-color: #ffffff; text-align: center;">SEPARATION</th>
                                <th style="border-color: #ffffff; text-align: center;">ACTIONS</th>
                            </tr>
                            <tr>
                                <th style="border-color: #ffffff; min-width: 120px;">From</th>
                                <th style="border-color: #ffffff; min-width: 120px;">To</th>
                                <th style="border-color: #ffffff; min-width: 250px;">Designation</th>
                                <th style="border-color: #ffffff; min-width: 120px;">Status</th>
                                <th style="border-color: #ffffff; min-width: 120px;">Salary</th>
                                <th style="border-color: #ffffff; min-width: 200px;">Station/Place of Assignment</th>
                                <th style="border-color: #ffffff; min-width: 150px;">w/o Pay</th>
                                <th style="border-color: #ffffff; min-width: 120px;">Date</th>
                                <th style="border-color: #ffffff; min-width: 150px;">Cause</th>
                                <th style="border-color: #ffffff; min-width: 100px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($serviceRecords as $record)
                                <tr>
                                    <td>{{ $record->service_from ? $record->service_from->format('Y-m-d') : '' }}</td>
                                    <td>{{ $record->service_to ? $record->service_to->format('Y-m-d') : '' }}</td>
                                    <td>{{ $record->appointment_designation }}</td>
                                    <td>{{ $record->appointment_status }}</td>
                                    <td class="text-right">{{ number_format($record->appointment_salary, 2) }}</td>
                                    <td>{{ $record->station_place }}</td>
                                    <td>{{ $record->leave_of_absence }}</td>
                                    <td class="text-xs">{{ $record->separation_date ? $record->separation_date->format('Y-m-d') : '' }}</td>
                                    <td>{{ $record->separation_cause }}</td>
                                    <td>
                                        <div class="flex items-center space-x-1">
                                            <button onclick="showEditForm({{ $record->id }})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 p-1" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <form method="POST" action="{{ route('employees.service_record.delete', [$employee->id, $record->id]) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this record?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 p-1" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Form Row (Hidden by default) -->
                                <tr id="editRow{{ $record->id }}" class="hidden bg-blue-50 dark:bg-gray-700">
                                    <td colspan="10" class="p-4">
                                        <form method="POST" action="{{ route('employees.service_record.update', [$employee->id, $record->id]) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Service From *</label>
                                                    <input type="date" name="service_from" value="{{ $record->service_from ? $record->service_from->format('Y-m-d') : '' }}" 
                                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" required>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Service To</label>
                                                    <input type="date" name="service_to" value="{{ $record->service_to ? $record->service_to->format('Y-m-d') : '' }}" 
                                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Designation *</label>
                                                    <input type="text" name="appointment_designation" value="{{ $record->appointment_designation }}" 
                                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" required>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status *</label>
                                                    <input type="text" name="appointment_status" value="{{ $record->appointment_status }}" 
                                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" required>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Salary *</label>
                                                    <input type="number" step="0.01" name="appointment_salary" value="{{ $record->appointment_salary }}" 
                                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" required>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Station/Place *</label>
                                                    <input type="text" name="station_place" value="{{ $record->station_place }}" 
                                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" required>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leave of Absence w/o Pay</label>
                                                    <input type="text" name="leave_of_absence" value="{{ $record->leave_of_absence }}" 
                                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Separation Date</label>
                                                    <input type="date" name="separation_date" value="{{ $record->separation_date ? $record->separation_date->format('Y-m-d') : '' }}" 
                                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Separation Cause</label>
                                                    <input type="text" name="separation_cause" value="{{ $record->separation_cause }}" 
                                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                                                </div>
                                            </div>
                                            <div class="flex justify-end space-x-2">
                                                <button type="button" onclick="hideEditForm({{ $record->id }})" 
                                                        class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors">
                                                    Cancel
                                                </button>
                                                <button type="submit" class="bg-[#198f51] text-white px-4 py-2 rounded-md hover:bg-[#156b3f] transition-colors">
                                                    Update Record
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-gray-500 dark:text-gray-400 py-8">
                                        No service records found. Click "Add New Record" to create one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add New Record Form (Hidden by default) -->
        <div id="addRecordForm" class="hidden bg-white dark:bg-[#282828] overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-[#198f51]">ADD NEW SERVICE RECORD</h3>
                    <button onclick="document.getElementById('addRecordForm').classList.add('hidden')" 
                            class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('employees.service_record.store', $employee->id) }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Service From *</label>
                            <input type="date" name="service_from" value="{{ old('service_from') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Service To</label>
                            <input type="date" name="service_to" value="{{ old('service_to') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Designation *</label>
                            <input type="text" name="appointment_designation" value="{{ old('appointment_designation') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status *</label>
                            <input type="text" name="appointment_status" value="{{ old('appointment_status') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" 
                                   placeholder="e.g., Permanent, Temporary, Casual" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Salary *</label>
                            <input type="number" step="0.01" name="appointment_salary" value="{{ old('appointment_salary') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Station/Place of Assignment *</label>
                            <input type="text" name="station_place" value="{{ old('station_place') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Leave of Absence w/o Pay</label>
                            <input type="text" name="leave_of_absence" value="{{ old('leave_of_absence') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Separation Date</label>
                            <input type="date" name="separation_date" value="{{ old('separation_date') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Separation Cause</label>
                            <input type="text" name="separation_cause" value="{{ old('separation_cause') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" 
                                   placeholder="e.g., Resignation, Retirement">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="document.getElementById('addRecordForm').classList.add('hidden')" 
                                class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="bg-[#198f51] text-white px-4 py-2 rounded-md hover:bg-[#156b3f] transition-colors">
                            Save Record
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Back Button -->
        <div>
            <a href="{{ route('employees.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                ← Back to Employees
            </a>
        </div>
    </div>
</div>

<script>
    function showEditForm(recordId) {
        document.getElementById('editRow' + recordId).classList.remove('hidden');
    }

    function hideEditForm(recordId) {
        document.getElementById('editRow' + recordId).classList.add('hidden');
    }
</script>
@endsection
