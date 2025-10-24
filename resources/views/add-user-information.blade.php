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

        @php
            $isEdit = isset($employee);
            $formAction = $isEdit ? route('employees.update', $employee->id) : route('employees.store');
        @endphp

        <!-- Form Container (uses POST for store and PUT for update via method spoofing) -->
        <div class="bg-white dark:bg-[#1c1c1d] overflow-hidden shadow-sm sm:rounded-lg border-2" id="formContainer" style="border-color: #2bb16b;">
            <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">
                <form method="POST" action="{{ $formAction }}" id="profileForm">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif

                    {{-- Existing info panel removed: values are shown in the input fields above when $employee exists --}}

                    <!-- Personal Information Section -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-green-600 dark:text-green-400 mb-4">Personal Information</h3>
                        <x-primary-text-input name="name" label="Full Name" :value="old('name', $employee->name ?? '')" />
                        <x-primary-text-input name="age" type="number" label="Age" :value="old('age', $employee->age ?? '')" />
                        <x-primary-text-input name="date_of_birth" type="date" label="Date of Birth" :value="old('date_of_birth', $employee->date_of_birth ?? '')" />
                        <x-primary-text-input name="place_of_birth" label="Place of Birth" :value="old('place_of_birth', $employee->place_of_birth ?? '')" />
                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center">
                            <label for="sex" class="w-full sm:w-1/3 font-medium custom-label sm:pr-4 mb-1 sm:mb-0">Sex :</label>
                            <select name="sex" id="sex" class="flex-1 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm">
                                <option value="">Select Gender</option>
                                <option value="Male" {{ (old('sex', $employee->sex ?? '') === 'Male') ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ (old('sex', $employee->sex ?? '') === 'Female') ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                    </div>
                    <!-- Employment Information Section -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-green-600 dark:text-green-400 mb-4">Employment Information</h3>
                        <x-primary-text-input name="department" label="Department" :value="old('department', $employee->department ?? '')" />
                        <x-primary-text-input name="job_title" label="Job Title" :value="old('job_title', $employee->job_title ?? '')" />
                        <x-primary-text-input name="designation" label="Designation" :value="old('designation', $employee->designation ?? '')" />
                        <x-primary-text-input name="place_of_assignment" label="Place of Assignment" :value="old('place_of_assignment', $employee->place_of_assignment ?? '')" />
                        <x-primary-text-input name="start_date" type="date" label="Start Date" :value="old('start_date', $employee->start_date ?? '')" />
                        <x-primary-text-input name="salary" type="number" step="0.01" label="Salary" :value="old('salary', $employee->salary ?? '')" />
                        <x-primary-text-input name="status" label="Status" :value="old('status', $employee->status ?? '')" />
                    </div>
                    <div class="submit-container">
                        <button type="button" class="custom-submit-btn px-6 py-2 rounded-md font-medium" id="submitButton">
                            {{ $isEdit ? 'Update Employee Data' : 'Add Employee Data' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Confirmation / Error Modal -->
<div id="confirmationModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white dark:bg-[#1c1c1d] rounded-lg shadow-lg max-w-lg w-full mx-4">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 id="confirmationModalTitle" class="text-lg font-semibold">Status</h3>
            <button id="confirmationModalClose" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <div class="p-4">
            <p id="confirmationModalMessage" class="text-sm text-gray-700 dark:text-gray-300"></p>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-gray-700 text-right">
            <button id="confirmationModalOk" class="px-4 py-2 rounded bg-green-600 text-white">OK</button>
        </div>
    </div>
</div>

<script>
$(function() {
    function showModal(title, message, onOk) {
        $('#confirmationModalTitle').text(title);
        $('#confirmationModalMessage').text(message);
        $('#confirmationModal').removeClass('hidden').addClass('flex');

        function close(handler) {
            $('#confirmationModal').removeClass('flex').addClass('hidden');
            $('#confirmationModalClose').off('click', handler);
            $('#confirmationModalOk').off('click', handler);
        }

        var handler = function() {
            close(handler);
            if (typeof onOk === 'function') onOk();
        };

        $('#confirmationModalClose').on('click', handler);
        $('#confirmationModalOk').on('click', handler);
    }

    $('#submitButton').on('click', function(e) {
        e.preventDefault();
        var form = $('#profileForm');

        // Determine HTTP method: default is POST; if form contains _method=PUT then use POST with _method
        var methodInput = form.find('input[name="_method"]');
        var httpMethod = methodInput.length ? methodInput.val().toUpperCase() : 'POST';

        var url = form.attr('action');

        // Build form data
        var formData = form.serialize();

        $.ajax({
            url: url,
            method: 'POST', // always POST because Laravel accepts method spoofing
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            success: function(response) {
                // If server returns JSON with success flag
                var title = 'Success';
                var message = 'Information saved successfully.';
                if (response && response.success === true && response.message) {
                    message = response.message;
                } else if (response && response.redirect) {
                    // Follow redirect if provided
                    window.location.href = response.redirect;
                    return;
                }

                showModal(title, message, function() {
                    // On OK: reload the page to show updated info
                    location.reload();
                });
            },
            error: function(xhr) {
                let msg = 'An error occurred.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    // try to parse validation errors
                    try {
                        var json = JSON.parse(xhr.responseText);
                        if (json && json.errors) {
                            msg = Object.values(json.errors).flat().join('\n');
                        }
                    } catch (e) {
                        // ignore
                    }
                }

                showModal('Error', msg, function() {
                    // On OK for error, keep on the same page and allow user to correct
                });
            }
        });
    });
});
</script>
@endsection
