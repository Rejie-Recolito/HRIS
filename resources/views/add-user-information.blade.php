@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-sm sm:text-xl text-white dark:text-white leading-tight flex items-center whitespace-nowrap">
        <span role="img" aria-hidden="true" class="inline-block w-6 h-6 mr-2" style="background-color:#ffffff; -webkit-mask-image: url({{ asset('images/icons/profile-icon.png') }}); -webkit-mask-repeat: no-repeat; -webkit-mask-position: center; -webkit-mask-size: contain; mask-image: url({{ asset('images/icons/profile-icon.png') }}); mask-repeat: no-repeat; mask-position: center; mask-size: contain;"></span>
        {{ __('EMPLOYEE INFORMATION') }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="w-full lg:w-4/5 mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Form Container -->
        <div class="bg-white dark:bg-[#1c1c1d] overflow-hidden shadow-sm sm:rounded-lg border-2" style="border-color: #2bb16b;">
            <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">
                <form method="POST" action="{{ isset($employee) ? route('employees.update', $employee->id) : route('employees.store') }}" id="employeeForm">
                    @csrf
                    @if(isset($employee))
                        @method('PUT')
                    @endif

                    <div class="mb-3 md:mb-4">
                        <label class="block w-full font-bold text-sm md:text-lg mb-2 md:mb-4" style="color: #198f51 !important;">PERSONAL INFORMATION</label>
                        
                        <!-- Name Fields: Stack on mobile/tablet, inline on desktop -->
                        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 md:gap-8">
                            <div class="flex flex-col md:flex-row md:items-center gap-0.5 md:gap-2 flex-1">
                                <label for="lastname" class="text-[11px] md:text-sm font-bold whitespace-nowrap md:w-24">LAST NAME</label>
                                <input type="text" name="lastname" id="lastname" value="{{ old('lastname', $employee->lastname ?? '') }}" class="block w-full text-xs md:text-base border-1.5 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm employee-field py-1.5 md:py-2" required {{ isset($employee) ? 'readonly' : '' }}>
                            </div>
                            <div class="flex flex-col md:flex-row md:items-center gap-0.5 md:gap-2 flex-1">
                                <label for="firstname" class="text-[11px] md:text-sm font-bold whitespace-nowrap md:w-24">FIRST NAME</label>
                                <input type="text" name="firstname" id="firstname" value="{{ old('firstname', $employee->firstname ?? '') }}" class="block w-full text-xs md:text-base border-1.5 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm employee-field py-1.5 md:py-2" required {{ isset($employee) ? 'readonly' : '' }}>
                            </div>
                            <div class="flex flex-col md:flex-row md:items-center gap-0.5 md:gap-2 flex-1 md:ml-auto">
                                <label for="middlename" class="text-[11px] md:text-sm font-bold whitespace-nowrap md:w-24">MIDDLE NAME</label>
                                <input type="text" name="middlename" id="middlename" value="{{ old('middlename', $employee->middlename ?? '') }}" class="block w-full text-xs md:text-base border-1.5 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm employee-field py-1.5 md:py-2" required {{ isset($employee) ? 'readonly' : '' }}>
                            </div>
                        </div>

                        <!-- Demographic Fields: Stack on mobile/tablet, inline on desktop -->
                        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 md:gap-8 mt-2 md:mt-8">
                            <div class="flex flex-col md:flex-row md:items-center gap-0.5 md:gap-2 flex-1">
                                <label for="sex" class="text-[11px] md:text-sm font-bold whitespace-nowrap md:w-24">SEX</label>
                                <select name="sex" id="sex" class="block w-full text-xs md:text-base border-1.5 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm employee-field py-1.5 md:py-2" required {{ isset($employee) ? 'disabled' : '' }}>
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ old('sex', $employee->sex ?? '') === 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('sex', $employee->sex ?? '') === 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                                @if(isset($employee))
                                    <!-- When the select is disabled it won't be submitted, include a hidden input so validation receives the value -->
                                    <input type="hidden" name="sex" value="{{ old('sex', $employee->sex ?? '') }}">
                                @endif
                            </div>

                            <div class="flex flex-col md:flex-row md:items-center gap-0.5 md:gap-2 flex-1">
                                <label for="age" class="text-[11px] md:text-sm font-bold whitespace-nowrap md:w-24">AGE</label>
                                <input type="number" name="age" id="age" value="{{ old('age', $employee->age ?? '') }}" min="0" class="block w-full text-xs md:text-base border-1.5 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm employee-field py-1.5 md:py-2" required {{ isset($employee) ? 'readonly' : '' }}>
                            </div>
                            <div class="flex flex-col md:flex-row md:items-center gap-0.5 md:gap-2 flex-1 md:ml-auto">
                                <label for="date_of_birth" class="text-[11px] md:text-sm font-bold whitespace-nowrap md:w-24">BIRTH DATE</label>
                                <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $employee->date_of_birth ?? '') }}" class="block w-full text-xs md:text-base border-1.5 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm employee-field py-1.5 md:py-2" required {{ isset($employee) ? 'readonly' : '' }}>
                            </div>
                        </div>

                        <!-- Birthplace and Address: Stack on mobile/tablet, side-by-side on desktop -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-8 mt-2 md:mt-3">
                            <div class="w-full">
                                <label for="place_of_birth" class="block text-[11px] md:text-sm font-bold mb-0.5 md:mb-1">BIRTHPLACE</label>
                                <input type="text" name="place_of_birth" id="place_of_birth" value="{{ old('place_of_birth', $employee->place_of_birth ?? '') }}" class="mt-0.5 md:mt-1 block w-full text-xs md:text-base border-1.5 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm employee-field py-1.5 md:py-2" required {{ isset($employee) ? 'readonly' : '' }}>
                            </div>
                            <div class="w-full">
                                <label for="address" class="block text-[11px] md:text-sm font-bold mb-0.5 md:mb-1">ADDRESS</label>
                                <input type="text" name="address" id="address" value="{{ old('address', $employee->address ?? '') }}" class="mt-0.5 md:mt-1 block w-full text-xs md:text-base border-1.5 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm employee-field py-1.5 md:py-2" required {{ isset($employee) ? 'readonly' : '' }}>
                            </div>
                        </div>
                
                    </div>

                    <div class="mb-3 md:mb-4">
                        <label class="block w-full font-bold text-sm md:text-lg mb-2 md:mb-4" style="color: #198f51 !important;">CONTACT INFORMATION</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 md:gap-8">
                            <div>
                                <label for="phone_number" class="block text-[11px] md:text-sm font-bold mb-0.5 md:mb-1">PHONE NUMBER</label>
                                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $employee->phone_number ?? '') }}" class="mt-0.5 md:mt-1 block w-full text-xs md:text-base border-1.5 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm employee-field py-1.5 md:py-2" required {{ isset($employee) ? 'readonly' : '' }}>
                            </div>
                            <div>
                                <label for="email_address" class="block text-[11px] md:text-sm font-bold mb-0.5 md:mb-1">EMAIL ADDRESS</label>
                                <input type="email" name="email_address" id="email_address" value="{{ old('email_address', $employee->email_address ?? '') }}" class="mt-0.5 md:mt-1 block w-full text-xs md:text-base border-1.5 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm employee-field py-1.5 md:py-2" required pattern="[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}" {{ isset($employee) ? 'readonly' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 md:mb-4">
                        <label class="block w-full font-bold text-sm md:text-lg mb-2 md:mb-4" style="color: #198f51 !important;">EMPLOYMENT INFORMATION</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 md:gap-8">
                            <div>
                                <label for="department" class="block text-[11px] md:text-sm font-bold mb-0.5 md:mb-1">DEPARTMENT</label>
                                <input type="text" name="department" id="department" value="{{ old('department', $employee->department ?? '') }}" class="mt-0.5 md:mt-1 block w-full text-xs md:text-base border-1.5 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm employee-field py-1.5 md:py-2" required {{ isset($employee) ? 'readonly' : '' }}>
                            </div>
                            <div>
                                <label for="designation" class="block text-[11px] md:text-sm font-bold mb-0.5 md:mb-1">DESIGNATION</label>
                                <input type="text" name="designation" id="designation" value="{{ old('designation', $employee->designation ?? '') }}" class="mt-0.5 md:mt-1 block w-full text-xs md:text-base border-1.5 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm employee-field py-1.5 md:py-2" required {{ isset($employee) ? 'readonly' : '' }}>
                            </div>
                            <div>
                                <label for="job_title" class="block text-[11px] md:text-sm font-bold mb-0.5 md:mb-1">JOB TITLE</label>
                                <input type="text" name="job_title" id="job_title" value="{{ old('job_title', $employee->job_title ?? '') }}" class="mt-0.5 md:mt-1 block w-full text-xs md:text-base border-1.5 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm employee-field py-1.5 md:py-2" required {{ isset($employee) ? 'readonly' : '' }}>
                            </div>
                            <div>
                                <label for="place_of_assignment" class="block text-[11px] md:text-sm font-bold mb-0.5 md:mb-1">PLACE OF ASSIGNMENT</label>
                                <input type="text" name="place_of_assignment" id="place_of_assignment" value="{{ old('place_of_assignment', $employee->place_of_assignment ?? '') }}" class="mt-0.5 md:mt-1 block w-full text-xs md:text-base border-1.5 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm employee-field py-1.5 md:py-2" required {{ isset($employee) ? 'readonly' : '' }}>
                            </div>
                        </div>
                        
                        <!-- Employment Details: Stack on mobile/tablet, inline on desktop -->
                        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 md:gap-8 mt-2 md:mt-8">
                            <div class="flex flex-col md:flex-row md:items-center gap-0.5 md:gap-2 flex-1">
                                <label for="start_date" class="text-[11px] md:text-sm font-bold whitespace-nowrap md:w-24">START DATE</label>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $employee->start_date ?? '') }}" class="block w-full text-xs md:text-base border-1.5 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm employee-field py-1.5 md:py-2" required {{ isset($employee) ? 'readonly' : '' }}>
                            </div>
                            <div class="flex flex-col md:flex-row md:items-center gap-0.5 md:gap-2 flex-1">
                                <label for="status" class="text-[11px] md:text-sm font-bold whitespace-nowrap md:w-24">STATUS</label>
                                <input type="text" name="status" id="status" value="{{ old('status', $employee->status ?? '') }}" class="block w-full text-xs md:text-base border-1.5 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm employee-field py-1.5 md:py-2" required {{ isset($employee) ? 'readonly' : '' }}>
                            </div>
                            <div class="flex flex-col md:flex-row md:items-center gap-0.5 md:gap-2 flex-1 md:pr-4">
                                <label for="salary" class="text-[11px] md:text-sm font-bold whitespace-nowrap md:w-24">SALARY</label>
                                <input type="number" step="0.01" name="salary" id="salary" value="{{ old('salary', $employee->salary ?? '') }}" class="block w-full text-xs md:text-base border-1.5 border-gray-300 input-field-border custom-input text-black dark:text-white rounded-xl shadow-sm employee-field py-1.5 md:py-2" required {{ isset($employee) ? 'readonly' : '' }}>
                            </div>
                        </div>
                    </div>

                </form>
                
                <!-- Edit Button (Bottom Right) or Add Button -->
                @if(isset($employee))
                <div class="flex justify-end mt-4">
                    <button type="button" id="editButton" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-3 md:px-4 py-2 rounded-md text-sm md:text-base font-medium transition-colors">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Info
                    </button>
                </div>
                @else
                <div class="flex justify-end mt-4">
                    <button type="button" id="addButton" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-3 md:px-4 py-2 rounded-md text-sm md:text-base font-medium transition-colors">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Employee Data
                    </button>
                </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons (Outside Form, Hidden by Default) -->
        @if(isset($employee))
        <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-3 mt-4" id="actionButtons" style="display: none;">
            <button type="button" id="cancelButton" class="flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white px-3 md:px-4 py-2 rounded-md text-sm md:text-base font-medium transition-colors">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cancel Edit
            </button>
            <button type="button" class="flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-3 md:px-4 py-2 rounded-md text-sm md:text-base font-medium transition-colors" id="updateButton">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Update Employee
            </button>
        </div>
        @endif

        <!-- Success Modal -->
        <div id="successModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50 px-4">
            <div class="bg-white dark:bg-[#1c1c1d] p-4 md:p-6 rounded-lg shadow-lg border-2 w-full max-w-md" style="border-color: #2bb16b;">
                <div class="text-center">
                    <svg class="w-10 h-10 md:w-12 md:h-12 mx-auto mb-3 md:mb-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <h2 class="text-base md:text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Successfully {{ isset($employee) ? 'Updated' : 'Added' }}!</h2>
                    <p class="text-sm md:text-base text-gray-600 dark:text-gray-300 mb-3 md:mb-4">Employee details have been {{ isset($employee) ? 'updated' : 'added' }} successfully.</p>
                    <button id="closeModalButton" class="custom-submit-btn px-4 py-2 rounded-md text-sm md:text-base">Continue</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    @if(isset($employee))
    let isEditing = false;

    document.getElementById('editButton').addEventListener('click', function() {
        isEditing = true;
        const fields = document.querySelectorAll('.employee-field');
        const actionButtons = document.getElementById('actionButtons');
        const editButton = document.getElementById('editButton');

        // Enable editing
        fields.forEach(field => {
            if (field.tagName === 'SELECT') {
                field.disabled = false;
            } else {
                field.readOnly = false;
            }
        });
        
        editButton.style.display = 'none';
        actionButtons.style.display = 'flex';
    });

    document.getElementById('cancelButton').addEventListener('click', function() {
        isEditing = false;
        const fields = document.querySelectorAll('.employee-field');
        const actionButtons = document.getElementById('actionButtons');
        const editButton = document.getElementById('editButton');

        // Disable editing
        fields.forEach(field => {
            if (field.tagName === 'SELECT') {
                field.disabled = true;
            } else {
                field.readOnly = true;
            }
        });
        
        editButton.style.display = 'flex';
        actionButtons.style.display = 'none';
        
        // Reset form to original values
        location.reload();
    });

    document.getElementById('updateButton').addEventListener('click', function() {
        document.getElementById('successModal').classList.remove('hidden');
    });

    document.getElementById('closeModalButton').addEventListener('click', function() {
        document.getElementById('employeeForm').submit();
    });
    @else
    document.getElementById('addButton').addEventListener('click', function() {
        document.getElementById('successModal').classList.remove('hidden');
    });

    document.getElementById('closeModalButton').addEventListener('click', function() {
        document.getElementById('employeeForm').submit();
    });
    @endif
</script>
@endsection
