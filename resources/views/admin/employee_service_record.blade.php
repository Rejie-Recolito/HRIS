@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white dark:text-white leading-tight">Service Record</h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2">
                            <form id="serviceRecordForm" method="POST" action="{{ route('service-records.update', $serviceRecord->id) }}">
                                @csrf
                        @csrf

                        <!-- Service Period -->
                        <div class="mb-4">
                            <h3 class="font-semibold mb-2">Service Period</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block font-medium">Service From</label>
                                    <input type="date" name="service_from" value="{{ $serviceRecord->service_from ?? '' }}" class="w-full rounded border-gray-300" />
                                </div>
                                <div>
                                    <label class="block font-medium">Service To</label>
                                    <input type="date" name="service_to" value="{{ $serviceRecord->service_to ?? '' }}" class="w-full rounded border-gray-300" />
                                </div>
                            </div>
                        </div>

                        <!-- Appointment / Rank -->
                        <div class="mb-4">
                            <h3 class="font-semibold mb-2">Appointment</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block font-medium">Appointment Rank</label>
                                    <input type="text" name="appointment_rank" value="{{ $serviceRecord->appointment_rank ?? '' }}" class="w-full rounded border-gray-300" />
                                </div>
                                <div>
                                    <label class="block font-medium">Designation</label>
                                    <input type="text" name="appointment_designation" value="{{ $serviceRecord->appointment_designation ?? '' }}" class="w-full rounded border-gray-300" />
                                </div>
                                <div>
                                    <label class="block font-medium">Status</label>
                                    <input type="text" name="appointment_status" value="{{ $serviceRecord->appointment_status ?? '' }}" class="w-full rounded border-gray-300" />
                                </div>
                                <div>
                                    <label class="block font-medium">Monthly Base Pay</label>
                                    <input type="number" step="0.01" name="appointment_monthly_base_pay" value="{{ $serviceRecord->appointment_monthly_base_pay ?? '' }}" class="w-full rounded border-gray-300" />
                                </div>
                            </div>
                        </div>

                        <!-- Station / Place -->
                        <div class="mb-4">
                            <h3 class="font-semibold mb-2">Office / Station</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block font-medium">Station</label>
                                    <input type="text" name="station" value="{{ $serviceRecord->station ?? '' }}" class="w-full rounded border-gray-300" />
                                </div>
                                <div>
                                    <label class="block font-medium">Place</label>
                                    <input type="text" name="place" value="{{ $serviceRecord->place ?? '' }}" class="w-full rounded border-gray-300" />
                                </div>
                            </div>
                        </div>

                        <!-- Leave Without Pay removed per request -->
                        <div class="mb-4">
                            <h3 class="font-semibold mb-2">Leave of Absence</h3>
                            <div>
                                <label class="block font-medium">Leave of Absence</label>
                                <input type="text" name="leave_of_absence" value="{{ $serviceRecord->leave_of_absence ?? '' }}" class="w-full rounded border-gray-300" />
                            </div>
                        </div>

                        <!-- Separation -->
                        <div class="mb-4">
                            <h3 class="font-semibold mb-2">Separation</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block font-medium">Separation Date</label>
                                    <input type="date" name="separation_date" value="{{ $serviceRecord->separation_date ?? '' }}" class="w-full rounded border-gray-300" />
                                </div>
                                <div>
                                    <label class="block font-medium">Cause</label>
                                    <input type="text" name="separation_cause" value="{{ $serviceRecord->separation_cause ?? '' }}" class="w-full rounded border-gray-300" />
                                </div>
                            </div>
                        </div>

                        <!-- Basic Info -->
                        <div class="mb-4">
                            <h3 class="font-semibold mb-2">Basic Information</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block font-medium">Name</label>
                                    <input type="text" name="name" value="{{ $serviceRecord->name }}" class="w-full rounded border-gray-300" required />
                                </div>
                                <div>
                                    <label class="block font-medium">Age</label>
                                    <input type="number" name="age" value="{{ $serviceRecord->age }}" class="w-full rounded border-gray-300" />
                                </div>
                                <div>
                                    <label class="block font-medium">Job Title</label>
                                    <input type="text" name="job_title" value="{{ $serviceRecord->job_title }}" class="w-full rounded border-gray-300" />
                                </div>
                                <div>
                                    <label class="block font-medium">Office</label>
                                    <input type="text" name="office" value="{{ $serviceRecord->office }}" class="w-full rounded border-gray-300" />
                                </div>
                                <div>
                                    <label class="block font-medium">Salary</label>
                                    <input type="number" step="0.01" name="salary" value="{{ $serviceRecord->salary }}" class="w-full rounded border-gray-300" />
                                </div>
                                <div>
                                    <label class="block font-medium">Place of Assignment</label>
                                    <input type="text" name="place_of_assignment" value="{{ $serviceRecord->place_of_assignment }}" class="w-full rounded border-gray-300" />
                                </div>
                            </div>
                        </div>

                        <!-- Request Status removed from the form per updated workflow -->

                        <div class="mt-4">
                            <button type="button" id="addBtn" class="bg-yellow-500 text-white px-4 py-2 rounded mr-2">Add (Partial)</button>
                            <button type="submit" id="saveBtn" class="bg-green-600 text-white px-4 py-2 rounded">Save (Complete)</button>
                            @if($serviceRecord->user_id)
                                <a href="{{ route('service-records.export', $serviceRecord->user_id) }}" class="ml-2 inline-block bg-blue-600 text-white px-4 py-2 rounded">Download DOCX</a>
                            @endif
                        </div>
                                </form>
                        </div>

                        <div class="lg:col-span-1 border-l pl-6">
                            <h3 class="font-semibold mb-4">All Service Records</h3>
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-xs text-gray-600">
                                        <th>Date</th>
                                        <th>Job Title</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($serviceRecords ?? collect() as $rec)
                                        <tr class="border-b">
                                            <td class="py-2">{{ $rec->service_from ? \Carbon\Carbon::parse($rec->service_from)->format('Y-m-d') : ($rec->created_at ? $rec->created_at->format('Y-m-d') : '') }}</td>
                                            <td class="py-2">
                                                <a href="{{ route('service-records.edit', ['id' => $rec->id]) }}" class="text-blue-600 hover:underline">{{ $rec->job_title ?? '—' }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <script>
                        (function(){
                            const form = document.getElementById('serviceRecordForm');
                            const addBtn = document.getElementById('addBtn');
                            if (addBtn && form) {
                                addBtn.addEventListener('click', function(){
                                    form.action = '{{ route('service-records.append', $serviceRecord->id) }}';
                                    // Ensure method is POST
                                    form.method = 'POST';
                                    form.submit();
                                });
                            }
                            // Save button retains form action for update
                        })();
                    </script>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
