@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white leading-tight flex items-center">
        <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        Process Request
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

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded dark:bg-red-900 dark:border-red-700 dark:text-red-200">
                {{ session('error') }}
            </div>
        @endif

        <!-- Employee Info Summary -->
        <div class="bg-white dark:bg-[#282828] overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4 text-[#198f51]">EMPLOYEE INFORMATION</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700 dark:text-gray-300">Name:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ strtoupper($employee->lastname) }}, {{ strtoupper($employee->firstname) }} {{ strtoupper($employee->middlename) }}</span>
                    </div>
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

        <!-- Service Records Table (Read-Only) -->
        <div class="bg-white dark:bg-[#282828] overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4 text-[#198f51]">SERVICE RECORD</h3>

                <div class="overflow-x-auto" style="max-width: 100%;">
                    <table class="admin-table" style="min-width: 1700px;">
                        <thead>
                            <tr>
                                <th colspan="2" style="border-color: #ffffff; text-align: center;">SERVICE<br>(Inclusive Dates)</th>
                                <th colspan="3" style="border-color: #ffffff; text-align: center;">RECORD OF APPOINTMENT</th>
                                <th style="border-color: #ffffff; text-align: center;">OFFICE ENTITY/DIV</th>
                                <th style="border-color: #ffffff; text-align: center;">LEAVE OF ABSENCE<br>W/O PAY</th>
                                <th colspan="2" style="border-color: #ffffff; text-align: center;">SEPARATION</th>
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-gray-500 dark:text-gray-400 py-8">
                                        No service records found. Click "Add/Edit Records" to add service records for this employee.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white dark:bg-[#282828] overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex justify-between items-center">
                    <a href="{{ route('service-record-requests.index') }}" 
                       class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                        ← Back to Requests
                    </a>
                    <div class="flex space-x-2">
                        <a href="{{ route('employees.service_record', $employee->id) }}" 
                           class="bg-[#198f51] text-white px-6 py-2 rounded-md hover:bg-[#156b3f] transition-colors">
                            Add/Edit Records
                        </a>
                        <form method="POST" action="{{ route('service-records.generate-document', $req->id) }}">
                            @csrf
                            <button type="submit" 
                                    class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                Generate
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
