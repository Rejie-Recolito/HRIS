@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white dark:text-white leading-tight flex items-center">
        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
        </svg>
        {{ __('SERVICE RECORD REQUEST') }}
    </h2>
@endsection

@section('content')
<style>
    
</style>

@php
    $lastServiceRecord = Auth::user()->serviceRecords()->latest()->first();
@endphp

<div class="py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Styled text section -->
            <div class="text-center mb-8 bg-white dark:bg-[#1c1c1d] rounded-lg border border-gray-700 dark:border-gray-600">
                <div class="flex items-center justify-center mb-3">
                    <svg class="w-7 h-7 sm:w-9 sm:h-9 mr-2 sm:mr-3 text-green-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                    </svg>
                    @if ($lastServiceRecord && $lastServiceRecord->request_status === 'pending')
                        <h1 class="font-bold custom-label custom-heading mb-0" style="margin-top: 0 !important;">Request Status</h1>
                    @else
                        <h1 class="font-bold custom-label custom-heading mb-0" style="margin-top: 0 !important;">Request Form</h1>
                    @endif
                </div>
                @if ($lastServiceRecord && $lastServiceRecord->request_status === 'pending')
                    <p class="text-base sm:text-lg text-gray-600 dark:text-gray-300 mb-2">You have submitted a service record request.</p>
                @else
                    <p class="text-base sm:text-lg text-gray-600 dark:text-gray-300 mb-2">Fill the required fields below to request for a service record.</p>
                @endif
                <div class="w-24 h-1 bg-green-600 mx-auto rounded"></div>
            </div>
        @if ($lastServiceRecord && $lastServiceRecord->request_status === 'pending')
            <div class="text-center mb-8 bg-white dark:bg-[#1c1c1d] p-6 rounded-lg shadow-md">
                <h2 class="text-lg font-semibold text-green-600 dark:text-green-400 mb-4">Your service record request is currently being processed.</h2>
                <p class="text-gray-700 dark:text-gray-300">You may submit new request after claiming the service record.</p>
            </div>
        @else
            <div class="bg-white dark:bg-[#1c1c1d] overflow-hidden shadow-sm sm:rounded-lg border-2" style="border-color: #2bb16b;">
                <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">
                    <form x-ref="serviceForm" method="POST" action="{{ route('service-records.store') }}" @submit.prevent="showOverlay = true">
                        @csrf

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                            <label for="name" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Full Name :</label>
                            <input type="text" name="name" id="name" placeholder="Last Name, First Name, Middle Name" class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                        </div>

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                            <label for="age" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Age :</label>
                            <input type="number" name="age" id="age" class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                        </div>

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                            <label for="salary" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Salary :</label>
                            <input type="number" step="0.01" name="salary" id="salary" class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                        </div>

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                            <label for="date_of_birth" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Date of Birth :</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                        </div>

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                            <label for="job_title" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Job Title :</label>
                            <input type="text" name="job_title" id="job_title" class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                        </div>

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                            <label for="place_of_birth" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Place of Birth :</label>
                            <input type="text" name="place_of_birth" id="place_of_birth" class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                        </div>

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                            <label for="office" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Office :</label>
                            <input type="text" name="office" id="office" class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                        </div>

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                            <label for="status" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Status :</label>
                            <input type="text" name="status" id="status" class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                        </div>

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                            <label for="date_of_service" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Date of Service :</label>
                            <input type="date" name="date_of_service" id="date_of_service" class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                        </div>

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                            <label for="place_of_assignment" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Place of Assignment :</label>
                            <input type="text" name="place_of_assignment" id="place_of_assignment" class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                        </div>

                        <div class="submit-container">
                            <button type="submit" class="custom-submit-btn px-4 py-2 rounded-md">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection