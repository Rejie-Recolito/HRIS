@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-sm sm:text-xl text-white dark:text-white leading-tight flex items-center whitespace-nowrap">
        <img src="{{ asset('Images/leave-icon.svg') }}" class="w-8 h-8 mr-3 header-icon" alt="Leave Icon">
        {{ __('LEAVE APPLICATION') }}
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
        color: #000000 !important; /* Dark gray/black in light mode */
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
        filter: brightness(0) invert(1);
    }
    
    /* Form header icon alignment */
    .form-header-icon {
        margin-top: 2px;
    }
</style>

<div class="py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Styled text section -->
        <div class="text-center mb-8 bg-white dark:bg-[#1c1c1d] rounded-lg border border-gray-700 dark:border-gray-600">
            <div class="flex items-center justify-center mb-3">
                <img src="{{ asset('images/leave-icon.svg') }}" class="w-7 h-7 sm:w-9 sm:h-9 mr-2 sm:mr-3 form-header-icon" alt="Leave Icon">
                @if(isset($lastApplication) && in_array($lastApplication->status, ['Under Review', 'Submitted', 'Approved', 'Denied']) && !session('acknowledged_leave_' . $lastApplication->id))
                    <h1 class="font-bold custom-label custom-heading mb-0" style="margin-top: 0 !important;">Leave Application Status</h1>
                @else
                    <h1 class="font-bold custom-label custom-heading mb-0" style="margin-top: 0 !important;">Leave Application Form</h1>
                @endif
            </div>
            @if(isset($lastApplication) && in_array($lastApplication->status, ['Under Review', 'Submitted', 'Approved', 'Denied']) && !session('acknowledged_leave_' . $lastApplication->id))
                <p class="text-base sm:text-lg text-gray-600 dark:text-gray-300 mb-2">
                    @if($lastApplication->status === 'Approved')
                        Your leave application has been approved.
                    @elseif($lastApplication->status === 'Denied')
                        Your leave application has been denied.
                    @else
                        You have submitted a leave application.
                    @endif
                </p>
            @else
                <p class="text-base sm:text-lg text-gray-600 dark:text-gray-300 mb-2">Fill the required fields below to submit your leave application.</p>
            @endif
            <div class="w-24 h-1 bg-green-600 mx-auto rounded"></div>
        </div>
        
        @if(isset($lastApplication) && in_array($lastApplication->status, ['Under Review', 'Submitted', 'Approved', 'Denied']) && !session('acknowledged_leave_' . $lastApplication->id))
            <div class="w-full flex items-center justify-center mb-8">
                <div class="bg-white dark:bg-[#1c1c1d] p-6 rounded-lg shadow-md text-center max-w-md w-full border-2" style="border-color: {{ $lastApplication->status === 'Denied' ? '#dc2626' : '#2bb16b' }};">
                    <div class="text-lg font-semibold mb-4" style="color: {{ $lastApplication->status === 'Denied' ? '#dc2626' : '#198f51' }};">
                        Status: 
                        @if($lastApplication->status === 'Approved')
                            Leave Approved
                        @elseif($lastApplication->status === 'Denied')
                            Leave Denied
                        @else
                            {{ $lastApplication->status }}
                        @endif
                    </div>
                    @if(in_array($lastApplication->status, ['Approved', 'Denied']))
                        <form method="POST" action="{{ route('leave.user.acknowledge', $lastApplication->id) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center px-6 py-2 text-white text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2" style="background-color: {{ $lastApplication->status === 'Denied' ? '#dc2626' : '#198f51' }}; hover:background-color: {{ $lastApplication->status === 'Denied' ? '#b91c1c' : '#166534' }};">
                                OK
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-[#1c1c1d] overflow-hidden shadow-sm sm:rounded-lg border-2" style="border-color: #2bb16b;">
                <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">
                    @livewire('leave-application-form')
                </div>
            </div>
        @endif
    </div>
</div>

@endsection