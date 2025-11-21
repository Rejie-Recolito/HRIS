@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white dark:text-white leading-tight flex items-center">
        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        {{ __('EMPLOYEES') }}
    </h2>
@endsection

@section('content')
    <style>
        body { color: white; }
        .overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); display: none; justify-content: center; align-items: center; z-index: 50; }
        .overlay .form-container { background-color: white; padding: 20px; border-radius: 8px; width: 90%; max-width: 500px; }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="card-bg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h2 class="text-xl font-bold mb-4">LGU-Bulusan Employees</h2>

                    <!-- Filter Input -->
                    <div class="mb-4">
                        <input type="text" id="employeeFilter" placeholder="Search employee..." class="border-gray-300 rounded-xl shadow-sm text-gray-900" style="width:30%">
                    </div>

                    <table class="admin-table table-auto mb-6">
                        <colgroup>
                            <col />
                            <col />
                            <col style="width:150px"/>
                            <col />
                            <col />
                            <col />
                            <col />
                        </colgroup>
                        <thead>
                            <tr>
                                <th>NAME</th>
                                <th>DEPARTMENT</th>
                                <th>JOB TITLE</th>
                                <th>START DATE</th>
                                <th>STATUS</th>
                                <th>SEX</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody id="employeeTable">
                            @foreach($employees as $employee)
                                <tr>
                                    <td class="whitespace-nowrap">{{ $employee->lastname }} {{ $employee->firstname }} {{ $employee->middlename }}</td>
                                    <td>{{ $employee->department }}</td>
                                    <td>{{ $employee->job_title }}</td>
                                    <td>{{ $employee->start_date }}</td>
                                    <td>{{ $employee->status }}</td>
                                    <td>{{ $employee->sex }}</td>
                                    <td>
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('employees.edit', $employee->id) }}" 
                                               aria-label="View/Edit employee profile" 
                                               title="View/Edit Employee Info" 
                                               class="inline-flex items-center justify-center w-8 h-8 rounded hover:bg-blue-100">
                                                <span role="img" aria-hidden="true" class="inline-block w-5 h-5" style="background-color:#253D90; -webkit-mask-image: url({{ asset('images/icons/profile-icon.png') }}); -webkit-mask-repeat: no-repeat; -webkit-mask-position: center; -webkit-mask-size: contain; mask-image: url({{ asset('images/icons/profile-icon.png') }}); mask-repeat: no-repeat; mask-position: center; mask-size: contain;"></span>
                                            </a>

                                            <a href="{{ route('employees.service_record', $employee->id) }}" 
                                               aria-label="View/Edit service record" 
                                               title="View/Edit Service Record"
                                               class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm whitespace-nowrap">
                                                Service Record
                                            </a>

                                            <a href="{{ route('employees.leave_card', $employee->id) }}" 
                                               aria-label="View leave card for {{ $employee->lastname }}, {{ $employee->firstname }}" 
                                               title="View/Add Leave Credits" 
                                               class="inline-flex items-center px-3 py-1 bg-[#198f51] hover:bg-[#166534] text-white text-sm font-medium rounded-md shadow-sm whitespace-nowrap">
                                                Leave Card
                                            </a>

                                            <form method="POST" action="{{ route('employees.destroy', $employee->id) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        aria-label="Delete employee" 
                                                        title="Delete Employee" 
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded hover:bg-red-100 text-red-600"
                                                        onclick="return confirm('Are you sure you want to delete this employee?');">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m4 0H5" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

    <div id="overlay" class="overlay">
        <div class="form-container">
            <form method="POST" action="{{ route('employees.store') }}">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-gray-900">
                </div>

                <div class="mb-4">
                    <label for="department" class="block text-sm font-medium text-gray-700">Department</label>
                    <input type="text" name="department" id="department" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-gray-900">
                </div>

                <div class="mb-4">
                    <label for="job_title" class="block text-sm font-medium text-gray-700">Job Title</label>
                    <input type="text" name="job_title" id="job_title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-gray-900">
                </div>

                <div class="mb-4">
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-gray-900">
                </div>

                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <input type="text" name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-gray-900">
                </div>

                <div class="mb-4">
                    <label for="sex" class="block text-sm font-medium text-gray-700">Sex</label>
                    <select name="sex" id="sex" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-gray-900">
                        <option value="Male" {{ old('sex') === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('sex') === 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>

                <div class="flex justify-end">
                    <button type="button" id="closeOverlay" class="bg-gray-500 text-white px-4 py-2 rounded-md mr-2">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md">Add Employee</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addButton = document.getElementById('addEmployeeButton');
            if (addButton) addButton.addEventListener('click', function() { document.getElementById('overlay').style.display = 'flex'; });
            const closeOverlay = document.getElementById('closeOverlay');
            if (closeOverlay) closeOverlay.addEventListener('click', function() { document.getElementById('overlay').style.display = 'none'; });

            const employeeFilter = document.getElementById('employeeFilter');
            const employeeTable = document.getElementById('employeeTable');
            if (employeeFilter) {
                employeeFilter.addEventListener('input', function() {
                    const filter = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#employeeTable tr');
                    let hasMatch = false;
                    rows.forEach(row => {
                        const name = row.querySelector('td:first-child').textContent.toLowerCase();
                        const isVisible = name.includes(filter);
                        row.style.display = isVisible ? '' : 'none';
                        if (isVisible) hasMatch = true;
                    });
                    const noMatchMessage = document.getElementById('noMatchMessage');
                    if (noMatchMessage) noMatchMessage.style.display = hasMatch ? 'none' : 'block';
                });
            }
            // optional hover handlers were removed to avoid hiding the table accidentally
            const noMatchElement = document.createElement('div');
            noMatchElement.id = 'noMatchMessage';
            noMatchElement.textContent = 'There are no matching employees.';
            noMatchElement.style.display = 'none';
            noMatchElement.className = 'text-center text-gray-500 mt-6 w-full';
            const tableParent = document.querySelector('#employeeTable')?.parentElement;
            if (tableParent) tableParent.appendChild(noMatchElement);
        });
    </script>
@endsection
