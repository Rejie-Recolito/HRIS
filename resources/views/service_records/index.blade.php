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

    <table class="table-auto w-full border-collapse border border-gray-400">
        <thead>
            <tr>
                <th class="border border-gray-300 px-4 py-2">Name</th>
                <th class="border border-gray-300 px-4 py-2">Age</th>
                <th class="border border-gray-300 px-4 py-2">Salary</th>
                <th class="border border-gray-300 px-4 py-2">Date of Birth</th>
                <th class="border border-gray-300 px-4 py-2">Job Title</th>
                <th class="border border-gray-300 px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($serviceRecords as $record)
                <tr>
                    <td class="border border-gray-300 px-4 py-2">{{ $record->name }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $record->age }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $record->salary }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $record->date_of_birth }}</td>
                    <td class="border border-gray-300 px-4 py-2">{{ $record->job_title }}</td>
                    <td class="border border-gray-300 px-4 py-2">
                        <a href="#" class="text-blue-500">Edit</a> |
                        <a href="#" class="text-red-500">Delete</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4">No service records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection