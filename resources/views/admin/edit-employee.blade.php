@extends('layouts.app')

@section('header')
    <div class="flex items-center">
        <a href="{{ route('employees.index') }}" class="mr-4 text-white dark:text-white">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
        </a>
        <h2 class="font-semibold text-xl text-white dark:text-white leading-tight flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            {{ __('EDIT EMPLOYEE') }}
        </h2>
    </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-8 bg-white dark:bg-[#1c1c1d] rounded-lg border border-gray-700 dark:border-gray-600">
            <div class="flex items-center justify-center mb-3">
                <svg class="w-7 h-7 sm:w-9 sm:h-9 mr-2 sm:mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <h1 class="font-bold custom-label custom-heading mb-0" style="margin-top: 0 !important;">Edit Employee</h1>
            </div>
            <p class="text-base sm:text-lg text-gray-600 dark:text-gray-300 mb-2">Update employee information below.</p>
            <div class="w-24 h-1 bg-green-600 mx-auto rounded"></div>
        </div>

        <!-- Form Container -->
        <div class="bg-white dark:bg-[#1c1c1d] overflow-hidden shadow-sm sm:rounded-lg border-2" style="border-color: #2bb16b;">
            <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">
                <form method="POST" action="{{ route('employees.update', $employee->id) }}" id="editEmployeeForm">
                    @csrf
                    @method('PUT')

                    <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                        <label for="name" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Name :</label>
                        <input type="text" name="name" id="name" value="{{ $employee->name }}" class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                    </div>

                    <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                        <label for="department" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Department :</label>
                        <input type="text" name="department" id="department" value="{{ $employee->department }}" class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                    </div>

                    <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                        <label for="job_title" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Job Title :</label>
                        <input type="text" name="job_title" id="job_title" value="{{ $employee->job_title }}" class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                    </div>

                    <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                        <label for="start_date" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Start Date :</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $employee->start_date }}" class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                    </div>

                    <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                        <label for="status" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Status :</label>
                        <input type="text" name="status" id="status" value="{{ $employee->status }}" class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                    </div>

                    <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                        <label for="sex" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Sex :</label>
                        <select name="sex" id="sex" class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                            <option value="">Select Gender</option>
                            <option value="Male" {{ $employee->sex === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ $employee->sex === 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>

                    <div class="submit-container">
                        <button type="button" class="custom-submit-btn px-6 py-2 rounded-md font-medium" id="updateButton">
                            Update Employee
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
                    <p class="text-gray-600 dark:text-gray-300 mb-4">Employee details have been updated successfully.</p>
                    <button id="closeModalButton" class="custom-submit-btn px-4 py-2 rounded-md">Continue</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('updateButton').addEventListener('click', function() {
        document.getElementById('successModal').classList.remove('hidden');
    });

    document.getElementById('closeModalButton').addEventListener('click', function() {
        document.getElementById('editEmployeeForm').submit();
    });
</script>
@endsection