@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
        </svg>
        {{ __('Service Record Request') }}
    </h2>
@endsection

@section('content')
<style>
    .custom-border {
        border-color: #198f51 !important;
    }
    .custom-border:focus {
        border-color: #198f51 !important;
        box-shadow: 0 0 0 3px rgba(25, 143, 81, 0.1) !important;
    }
    .custom-input {
        color: #000000 !important; /* Black text in light mode */
        background-color: #ffffff !important; /* White background in light mode */
    }
    .dark .custom-input {
        color: #ffffff !important; /* White text in dark mode */
        background-color: #374151 !important; /* Dark background in dark mode */
    }
    /* Add specific label styling */
    .custom-label {
        color: #000000ff !important; /* Dark gray/black in light mode */
        font-size: 16px !important; /* Change this value to your desired size */
    }
    .dark .custom-label {
        color: #f9fafb !important; /* White in dark mode */
        font-size: 16px !important; /* Change this value to your desired size */
    }
    
    /* Custom heading styling */
    .custom-heading {
        font-size: 36px !important;
        margin-top: 24px !important; /* Adds spacing from top */
    }
    
    /* Mobile responsive label */
    @media (max-width: 640px) {
        .custom-label {
            font-size: 14px !important;
            white-space: nowrap !important;
        }
        .custom-heading {
            font-size: 28px !important;
        }
        .custom-input {
            font-size: 14px !important;
            padding: 8px 12px !important;
        }
    }
    
    /* Regular input placeholder */
    .custom-input::placeholder {
        color: #198f51 !important;
        opacity: 1 !important;
    }
    .dark .custom-input::placeholder {
        color: #f9fafb !important;
        opacity: 1 !important;
    }
    
    /* For empty date inputs - this shows the dd/mm/yyyy in green */
    .custom-input::-webkit-datetime-edit-fields-wrapper {
        color: #198f51 !important;
    }
    .dark .custom-input::-webkit-datetime-edit-fields-wrapper {
        color: #2bb16b !important; /* Lighter green for dark mode */
    }

    /* Date input when it has a value - this makes the actual date black */
    .custom-input[type="date"]:valid::-webkit-datetime-edit-fields-wrapper {
        color: #000000 !important; /* Black when date is selected */
    }
    .dark .custom-input[type="date"]:valid::-webkit-datetime-edit-fields-wrapper {
        color: #ffffff !important; /* White when date is selected in dark mode */
    }

    /* Style individual date components when they have values */
    .custom-input[type="date"]:valid::-webkit-datetime-edit-month-field,
    .custom-input[type="date"]:valid::-webkit-datetime-edit-day-field,
    .custom-input[type="date"]:valid::-webkit-datetime-edit-year-field {
        color: #000000 !important; /* Black when filled */
    }
    
    .dark .custom-input[type="date"]:valid::-webkit-datetime-edit-month-field,
    .dark .custom-input[type="date"]:valid::-webkit-datetime-edit-day-field,
    .dark .custom-input[type="date"]:valid::-webkit-datetime-edit-year-field {
        color: #ffffff !important; /* White when filled in dark mode */
    }

    /* Calendar icon styling */
    .custom-input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        opacity: 0.7;
        transition: all 0.2s ease;
    }
    
    .custom-input[type="date"]::-webkit-calendar-picker-indicator:hover {
        background-color: rgba(25, 143, 81, 0.1);
        opacity: 1;
    }
    
    .dark .custom-input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        opacity: 0.8;
        filter: brightness(1.2);
    }
    
    .dark .custom-input[type="date"]::-webkit-calendar-picker-indicator:hover {
        background-color: rgba(43, 177, 107, 0.1);
        opacity: 1;
        filter: brightness(1.4);
    }
    
    /* Custom submit button styling */
    .custom-submit-btn {
        background-color: #198f51 !important;
        color: white !important;
        transition: all 0.2s ease;
    }
    
    .custom-submit-btn:hover {
        background-color: #156b3f !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(25, 143, 81, 0.3);
    }
    
    .dark .custom-submit-btn {
        background-color: #2bb16b !important;
    }
    
    .dark .custom-submit-btn:hover {
        background-color: #22a55a !important;
    }
</style>

<div class="py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Styled text section -->
        <div class="text-center mb-8 bg-white rk:bg-white -6 rounded-lg dark:border-gray-700">
            <div class="flex items-center justify-center mb-3">
                <svg class="w-7 h-7 sm:w-9 sm:h-9 mr-2 sm:mr-3 text-green-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                </svg>
                <h1 class="font-bold custom-label custom-heading mb-0" style="margin-top: 0 !important;">Request Form</h1>
            </div>
            <p class="text-base sm:text-lg text-black-600 dark:text-white-400 mb-2">Fill the required fields below to request for a service record.</p>
            <div class="w-24 h-1 bg-green-600 mx-auto rounded"></div>
        </div>
        
        <div class="bg-white dark:bg-white-800 overflow-hidden shadow-sm sm:rounded-lg border-2" style="border-color: #2bb16b;">
            <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">
                <div x-data="{ showOverlay: false, submitForm() { this.$refs.serviceForm.submit(); } }">
                    <div x-show="showOverlay" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50">
                        <div class="bg-white p-6 rounded shadow-lg text-center">
                            <h2 class="text-lg font-semibold text-green-600 mb-4">Service request sent</h2>
                            <button @click="submitForm" class="bg-green-600 text-white px-4 py-2 rounded">OK</button>
                        </div>
                    </div>

                    <form x-ref="serviceForm" method="POST" action="{{ route('service-records.store') }}" @submit.prevent="showOverlay = true">
                        @csrf

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                            <label for="name" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Name :</label>
                            <input type="text" name="name" id="name" class="flex-1 border-gray-300 custom-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                        </div>

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                            <label for="age" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Age :</label>
                            <input type="number" name="age" id="age" class="flex-1 border-gray-300 custom-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                        </div>

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                            <label for="salary" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Salary :</label>
                            <input type="number" step="0.01" name="salary" id="salary" class="flex-1 border-gray-300 custom-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                        </div>

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                            <label for="date_of_birth" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Date of Birth :</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" class="flex-1 border-gray-300 custom-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                        </div>

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                            <label for="job_title" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Job Title :</label>
                            <input type="text" name="job_title" id="job_title" class="flex-1 border-gray-300 custom-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                        </div>

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                            <label for="place_of_birth" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Place of Birth :</label>
                            <input type="text" name="place_of_birth" id="place_of_birth" class="flex-1 border-gray-300 custom-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                        </div>

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                            <label for="office" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Office :</label>
                            <input type="text" name="office" id="office" class="flex-1 border-gray-300 custom-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                        </div>

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                            <label for="status" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Status :</label>
                            <input type="text" name="status" id="status" class="flex-1 border-gray-300 custom-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                        </div>

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                            <label for="date_of_service" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Date of Service :</label>
                            <input type="date" name="date_of_service" id="date_of_service" class="flex-1 border-gray-300 custom-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                        </div>

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                            <label for="place_of_assignment" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Place of Assignment :</label>
                            <input type="text" name="place_of_assignment" id="place_of_assignment" class="flex-1 border-gray-300 custom-border custom-input text-black dark:text-white rounded-xl shadow-sm" required>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="custom-submit-btn px-4 py-2 rounded-md">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection