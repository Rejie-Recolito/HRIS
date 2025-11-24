@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-3xl font-bold mb-4">Service Records</h1>

    <!-- Success Modal -->
    @if(session('success'))
        <div id="successModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white dark:bg-[#1c1c1d] p-8 rounded-lg shadow-lg border-2" style="border-color: #2bb16b; min-width: 400px;">
                <div class="text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <h2 class="text-xl font-medium text-gray-900 dark:text-gray-100 mb-3">Success!</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-6 text-base">{{ session('success') }}</p>
                    <button onclick="document.getElementById('successModal').style.display='none'" class="custom-submit-btn px-6 py-2 rounded-md">Continue</button>
                </div>
            </div>
        </div>
    @endif

    <table class="admin-table mb-6">
        <thead>
            <tr>
                <th>Name</th>
                <th>Age</th>
                <th>Salary</th>
                <th>Date of Birth</th>
                <th>Job Title</th>
                <th>Request Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($serviceRecords as $record)
                <tr>
                    <td>{{ $record->name }}</td>
                    <td>{{ $record->age }}</td>
                    <td>{{ $record->salary }}</td>
                    <td>{{ $record->date_of_birth }}</td>
                    <td>{{ $record->job_title }}</td>
                    <td>
                        @php
                            $req = \App\Models\ServiceRecordRequest::where('service_record_id', $record->id)->orderByDesc('updated_at')->first();
                        @endphp
                        {{ $req ? $req->request_status : 'N/A' }}
                    </td>
                    <td style="padding: 0 !important; text-align: center !important; vertical-align: middle !important;">
                        <div class="inline-flex flex-row items-center justify-center gap-2">
                            <a href="#" class="btn-edit px-4 py-2 text-base flex items-center justify-center">Process</a>
                            <a href="#" class="btn-delete px-4 py-2 text-base flex items-center justify-center">Delete</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4">No service records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection