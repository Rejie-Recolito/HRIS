@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
        <img src="{{ asset('images/leave-icon.svg') }}" class="w-8 h-8 mr-3 header-icon" alt="Leave Icon">
        {{ __('Leave Application') }}
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
    
    /* Center submit button */
    .submit-container {
        display: flex;
        justify-content: center;
        margin-top: 1.5rem;
    }
    
    /* Header icon alignment */
    .header-icon {
        display: inline-block;
        vertical-align: middle;
        margin-top: 2px;
    }
    
    /* Form header icon alignment */
    .form-header-icon {
        margin-top: 2px;
    }
</style>

<div class="py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Styled text section -->
        <div class="text-center mb-8 bg-white rk:bg-white -6 rounded-lg dark:border-gray-700">
            <div class="flex items-center justify-center mb-3">
                <img src="{{ asset('images/leave-icon.svg') }}" class="w-7 h-7 sm:w-9 sm:h-9 mr-2 sm:mr-3 form-header-icon" alt="Leave Icon">
                <h1 class="font-bold custom-label custom-heading mb-0" style="margin-top: 0 !important;">Leave Application Form</h1>
            </div>
            <p class="text-base sm:text-lg text-black-600 dark:text-white-400 mb-2">Fill the required fields below to submit your leave application.</p>
            <div class="w-24 h-1 bg-green-600 mx-auto rounded"></div>
        </div>
        
        <div class="bg-white dark:bg-white-800 overflow-hidden shadow-sm sm:rounded-lg border-2" style="border-color: #2bb16b;">
            <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">
                @livewire('leave-application-form')
            </div>
        </div>
    </div>
</div>
@endsection