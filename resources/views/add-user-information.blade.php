@extends('layouts.app')
    @section('header')
         <h2 class="font-semibold text-xl text-white dark:text-white leading-tight flex items-center">

        {{ __('EMPLOYEE PROFILE') }}
    </h2>
    @endsection
    @section('content')
   
    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 bg-white dark:bg-[#1c1c1d] rounded-lg border border-gray-700 dark:border-gray-600">
                @include('profile.partials.add-profile-picture')
                    <div class="flex items-center justify-center mb-3">
                        
                        <form method="POST" action="{{ route('employees.store') }}" id="profileForm">
                        @csrf
                        <p class="mt-1 text-sm description-in-profile-page">
                        {{ __('Personal Information') }}
                        </p>
                        <x-primary-text-input name="name" label="Name"/>
                        <x-primary-text-input name="age" type="number" label="Age"/>
                        <x-primary-text-input name="salary" type="number" step="0.01" label="Salary"/>
                        <x-primary-text-input name="date_of_birth" type="date" label="Date of Birth"/>
                        <x-primary-text-input name="place_of_birth" label="Place of Birth"/>
                        <p class="mt-1 text-sm description-in-profile-page">
                        {{ __('Employment') }}
                        </p>
                        <x-primary-text-input name="department" label="Department"/>
                        <x-primary-text-input name="job_title" label="Job Title"/>
                        <x-primary-text-input name="designation" label="Designation"/>
                        <x-primary-text-input name="place_of_assignment" label="Place of Assignment"/>
                        <x-primary-text-input name="start_date" type="date" label="Start Date"/>
                        <x-primary-text-input name="status" label="Status"/>

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                            <label for="sex" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Sex</label>
                            <select name="sex" id="sex" class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm">
                                <option value="Male" {{ old('sex') === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('sex') === 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div class="flex justify-end">
                            <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-md" id="submitButton">Submit</button>
                        </div>
                        </form>


    <!-- Modal -->
                        <div id="successModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
                            <div class="bg-white p-6 rounded-lg shadow-lg">
                                <h2 class="text-lg font-medium">Successfully updated profile information</h2>
                                <div class="flex justify-end mt-4">
                                    <button id="closeModalButton" class="bg-blue-600 text-white px-4 py-2 rounded-md">Close</button>
                                </div>
                            </div>
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
