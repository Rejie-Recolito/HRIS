@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white dark:text-white leading-tight flex items-center">
    <svg class="w-6 h-6 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        {{ __('MY EMPLOYEE DATA') }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-8 bg-white dark:bg-[#1c1c1d] rounded-lg border border-gray-700 dark:border-gray-600">
            <div class="flex items-center justify-center mb-3">
                <svg class="w-7 h-7 sm:w-9 sm:h-9 mr-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <h1 class="font-bold custom-label custom-heading mb-0" style="margin-top: 0 !important;">Employee Information</h1>
            </div>
            <p class="text-base sm:text-lg text-gray-600 dark:text-gray-300 mb-2">Fill out your personal and employment information below.</p>
            <div class="w-24 h-1 bg-green-600 mx-auto rounded"></div>
        </div>

        <!-- Profile Picture Section -->
        <div class="text-center mb-8 bg-white dark:bg-[#1c1c1d] p-6 rounded-lg shadow-md">
            @include('profile.partials.add-profile-picture')
        </div>

        @if(isset($employee))
            <div class="bg-white dark:bg-[#1c1c1d] p-4 rounded-lg mb-4 border-2 dark:text-white" style="border-color: #198f51;">
                <h3 class="text-2xl font-extrabold text-green-700 dark:text-white mb-4 bg-transparent dark:bg-[#282828] px-2 py-2 rounded text-center flex items-center justify-center gap-2 uppercase">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7 mr-1 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-base sm:text-2xl font-extrabold uppercase tracking-wide text-center flex-1 text-green-700 dark:text-white whitespace-nowrap overflow-hidden text-ellipsis">Personal Information</span>
                </h3>
                <div class="mb-2 flex items-center gap-1 sm:gap-2 text-sm sm:text-base"><strong class="w-32 sm:w-48 text-left dark:text-white whitespace-nowrap">Name:</strong> <span class="flex-1 border border-green-600 rounded-xl px-2 sm:px-3 py-1.5 sm:py-2 bg-white dark:bg-[#1c1c1d] dark:text-white truncate">{{ $employee->name }}</span></div>
                <div class="mb-2 flex items-center gap-1 sm:gap-2 text-sm sm:text-base"><strong class="w-32 sm:w-48 text-left dark:text-white whitespace-nowrap">Age:</strong> <span class="flex-1 border border-green-600 rounded-xl px-2 sm:px-3 py-1.5 sm:py-2 bg-white dark:bg-[#1c1c1d] dark:text-white truncate">{{ $employee->age }}</span></div>
                <div class="mb-2 flex items-center gap-1 sm:gap-2 text-sm sm:text-base"><strong class="w-32 sm:w-48 text-left dark:text-white whitespace-nowrap">Date of Birth:</strong> <span class="flex-1 border border-green-600 rounded-xl px-2 sm:px-3 py-1.5 sm:py-2 bg-white dark:bg-[#1c1c1d] dark:text-white truncate">{{ $employee->date_of_birth }}</span></div>
                <div class="mb-2 flex items-center gap-1 sm:gap-2 text-sm sm:text-base"><strong class="w-32 sm:w-48 text-left dark:text-white whitespace-nowrap">Place of Birth:</strong> <span class="flex-1 border border-green-600 rounded-xl px-2 sm:px-3 py-1.5 sm:py-2 bg-white dark:bg-[#1c1c1d] dark:text-white truncate">{{ $employee->place_of_birth }}</span></div>
                <div class="mb-2 flex items-center gap-1 sm:gap-2 text-sm sm:text-base"><strong class="w-32 sm:w-48 text-left dark:text-white whitespace-nowrap">Sex:</strong> <span class="flex-1 border border-green-600 rounded-xl px-2 sm:px-3 py-1.5 sm:py-2 bg-white dark:bg-[#1c1c1d] dark:text-white truncate">{{ $employee->sex }}</span></div>
                <h3 class="text-2xl font-extrabold text-green-700 dark:text-white mt-8 mb-4 bg-transparent dark:bg-[#282828] px-2 py-2 rounded text-center flex items-center justify-center gap-2 uppercase">
                    <svg class="w-5 h-5 sm:w-7 sm:h-7 mr-1 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7V6a2 2 0 012-2h2a2 2 0 012 2v1m6 0V6a2 2 0 012-2h2a2 2 0 012 2v1M3 7h18a2 2 0 012 2v9a2 2 0 01-2 2H3a2 2 0 01-2-2V9a2 2 0 012-2zm5 4h8" />
                    </svg>
                    <span class="text-base sm:text-2xl font-extrabold uppercase tracking-wide text-center flex-1 text-green-700 dark:text-white whitespace-nowrap overflow-hidden text-ellipsis">Employment Information</span>
                </h3>
                <div class="mb-2 flex items-center gap-1 sm:gap-2 text-sm sm:text-base"><strong class="w-32 sm:w-48 text-left dark:text-white whitespace-nowrap">Department:</strong> <span class="flex-1 border border-green-600 rounded-xl px-2 sm:px-3 py-1.5 sm:py-2 bg-white dark:bg-[#1c1c1d] dark:text-white truncate">{{ $employee->department }}</span></div>
                <div class="mb-2 flex items-center gap-1 sm:gap-2 text-sm sm:text-base"><strong class="w-32 sm:w-48 text-left dark:text-white whitespace-nowrap">Job Title:</strong> <span class="flex-1 border border-green-600 rounded-xl px-2 sm:px-3 py-1.5 sm:py-2 bg-white dark:bg-[#1c1c1d] dark:text-white truncate">{{ $employee->job_title }}</span></div>
                <div class="mb-2 flex items-center gap-1 sm:gap-2 text-sm sm:text-base"><strong class="w-32 sm:w-48 text-left dark:text-white whitespace-nowrap">Designation:</strong> <span class="flex-1 border border-green-600 rounded-xl px-2 sm:px-3 py-1.5 sm:py-2 bg-white dark:bg-[#1c1c1d] dark:text-white truncate">{{ $employee->designation }}</span></div>
                <div class="mb-2 flex items-center gap-1 sm:gap-2 text-sm sm:text-base"><strong class="w-32 sm:w-48 text-left whitespace-nowrap dark:text-white">Place of Assignment:</strong> <span class="flex-1 border border-green-600 rounded-xl px-2 sm:px-3 py-1.5 sm:py-2 bg-white dark:bg-[#1c1c1d] dark:text-white truncate">{{ $employee->place_of_assignment }}</span></div>
                <div class="mb-2 flex items-center gap-1 sm:gap-2 text-sm sm:text-base"><strong class="w-32 sm:w-48 text-left dark:text-white whitespace-nowrap">Start Date:</strong> <span class="flex-1 border border-green-600 rounded-xl px-2 sm:px-3 py-1.5 sm:py-2 bg-white dark:bg-[#1c1c1d] dark:text-white truncate">{{ $employee->start_date }}</span></div>
                <div class="mb-2 flex items-center gap-1 sm:gap-2 text-sm sm:text-base"><strong class="w-32 sm:w-48 text-left dark:text-white whitespace-nowrap">Salary:</strong> <span class="flex-1 border border-green-600 rounded-xl px-2 sm:px-3 py-1.5 sm:py-2 bg-white dark:bg-[#1c1c1d] dark:text-white truncate">{{ $employee->salary }}</span></div>
                <div class="mb-2 flex items-center gap-1 sm:gap-2 text-sm sm:text-base"><strong class="w-32 sm:w-48 text-left dark:text-white whitespace-nowrap">Status:</strong> <span class="flex-1 border border-green-600 rounded-xl px-2 sm:px-3 py-1.5 sm:py-2 bg-white dark:bg-[#1c1c1d] dark:text-white truncate">{{ $employee->status }}</span></div>
            </div>
        @else
            <!-- Form Container -->
            <div class="bg-white dark:bg-[#1c1c1d] overflow-hidden shadow-sm sm:rounded-lg border-2" id="formContainer" style="border-color: #2bb16b;">
                <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('employees.store') }}" id="profileForm">
                        @csrf
                        <!-- Personal Information Section -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-green-600 dark:text-green-400 mb-4">Personal Information</h3>
                            <x-primary-text-input name="name" label="Full Name"/>
                            <x-primary-text-input name="age" type="number" label="Age"/>
                            <x-primary-text-input name="date_of_birth" type="date" label="Date of Birth"/>
                            <x-primary-text-input name="place_of_birth" label="Place of Birth"/>
                            <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                                <label for="sex" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Sex :</label>
                                <select name="sex" id="sex" class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm">
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ old('sex') === 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('sex') === 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                        </div>
                        <!-- Employment Information Section -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-green-600 dark:text-green-400 mb-4">Employment Information</h3>
                            <x-primary-text-input name="department" label="Department"/>
                            <x-primary-text-input name="job_title" label="Job Title"/>
                            <x-primary-text-input name="designation" label="Designation"/>
                            <x-primary-text-input name="place_of_assignment" label="Place of Assignment"/>
                            <x-primary-text-input name="start_date" type="date" label="Start Date"/>
                            <x-primary-text-input name="salary" type="number" step="0.01" label="Salary"/>
                            <x-primary-text-input name="status" label="Status"/>
                        </div>
                        <div class="submit-container">
                            <button type="button" class="custom-submit-btn px-6 py-2 rounded-md font-medium" id="submitButton">
                                Add Employee Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#submitButton').on('click', function(e) {
    e.preventDefault();
    var form = $('#profileForm');
    var formData = form.serialize();

    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: formData,
        headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val()},
        success: function(response) {
            if (response.success) {
                location.reload(); // Reload the page to show the info block
            }
        },
        error: function(xhr) {
            let msg = 'An error occurred.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            }
            alert('Error: ' + msg);
        }
    });
});
</script>
@endsection
