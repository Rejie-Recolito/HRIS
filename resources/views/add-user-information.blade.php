@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white dark:text-white leading-tight flex items-center">
        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        {{ __('EMPLOYEE PROFILE') }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-8 bg-white dark:bg-[#1c1c1d] rounded-lg border border-gray-700 dark:border-gray-600">
            <div class="flex items-center justify-center mb-3">
                <svg class="w-7 h-7 sm:w-9 sm:h-9 mr-2 sm:mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
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

        <!-- Form Container -->
        <div class="bg-white dark:bg-[#1c1c1d] overflow-hidden shadow-sm sm:rounded-lg border-2" style="border-color: #2bb16b;">
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
                            Submit Employee Information
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Success Modal -->
        <div id="successModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
            <div class="bg-white dark:bg-[#1c1c1d] p-6 rounded-lg shadow-lg border-2" style="border-color: #2bb16b;">
                <div class="text-center">
                    <svg class="w-12 h-12 mx-auto mb-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Successfully Updated!</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">Your employee profile information has been saved.</p>
                    <button id="closeModalButton" class="custom-submit-btn px-4 py-2 rounded-md">Continue</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('submitButton').addEventListener('click', function() {
        document.getElementById('successModal').classList.remove('hidden');
    });

    document.getElementById('closeModalButton').addEventListener('click', function() {
        document.getElementById('profileForm').submit();
    });
</script>
@endsection
