@extends('layouts.app')

@section('header')
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Employees') }}
        </h2>
@endsection

@section('content')
    <style>
        body {
            color: white;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h2 class="text-xl font-bold mb-4">Employees</h2>

                    <!-- Add Employee Button -->
                    <div class="mb-4">
                        <button id="addEmployeeButton" class="bg-green-600 text-white px-4 py-2 rounded-md">Add Employee</button>
                    </div>

                    <!-- Add Employee Form -->
                    <div id="addEmployeeForm" class="hidden mb-4">
                        <form method="POST" action="{{ route('employees.store') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-200">Name</label>
                                <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-gray-900">
                            </div>

                            <div class="mb-4">
                                <label for="department" class="block text-sm font-medium text-gray-200">Department</label>
                                <input type="text" name="department" id="department" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-gray-900">
                            </div>

                            <div class="mb-4">
                                <label for="job_title" class="block text-sm font-medium text-gray-200">Job Title</label>
                                <input type="text" name="job_title" id="job_title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-gray-900">
                            </div>

                            <div class="mb-4">
                                <label for="start_date" class="block text-sm font-medium text-gray-200">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-gray-900">
                            </div>

                            <div class="mb-4">
                                <label for="status" class="block text-sm font-medium text-gray-200">Status</label>
                                <input type="text" name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-gray-900">
                            </div>

                            <div class="mb-4">
                                <label for="sex" class="block text-sm font-medium text-gray-200">Sex</label>
                                <input type="text" name="sex" id="sex" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-gray-900">
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md">Submit</button>
                            </div>
                        </form>
                    </div>

                    <!-- Filter Input -->
                    <div class="mb-4">
                        <input type="text" id="employeeFilter" placeholder="Search employees..." class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <table class="min-w-full border text-sm mb-6">
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-700">
                                <th class="border px-2 py-1">Name</th>
                                <th class="border px-2 py-1">Department</th>
                                <th class="border px-2 py-1">Job Title</th>
                                <th class="border px-2 py-1">Start Date</th>
                                <th class="border px-2 py-1">Status</th>
                                <th class="border px-2 py-1">Sex</th>
                                <th class="border px-2 py-1">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="employeeTable">
                            @foreach($employees as $employee)
                                <tr>
                                    <td class="border px-2 py-1">{{ $employee->name }}</td>
                                    <td class="border px-2 py-1">{{ $employee->department }}</td>
                                    <td class="border px-2 py-1">{{ $employee->job_title }}</td>
                                    <td class="border px-2 py-1">{{ $employee->start_date }}</td>
                                    <td class="border px-2 py-1">{{ $employee->status }}</td>
                                    <td class="border px-2 py-1">{{ $employee->sex }}</td>
                                    <td class="border px-2 py-1">
                                        <a href="{{ route('employees.edit', $employee->id) }}" class="bg-blue-600 text-white px-2 py-1 rounded">Edit</a>
                                        <form method="POST" action="{{ route('employees.destroy', $employee->id) }}" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 text-white px-2 py-1 rounded">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if(empty($employees) || count($employees) === 0)
                        <div class="text-center text-gray-500">No employees found.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('addEmployeeButton').addEventListener('click', function() {
            const form = document.getElementById('addEmployeeForm');
            form.classList.toggle('hidden');
        });

        document.getElementById('employeeFilter').addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('#employeeTable tr');

            rows.forEach(row => {
                const name = row.querySelector('td:first-child').textContent.toLowerCase();
                row.style.display = name.includes(filter) ? '' : 'none';
            });
        });
    </script>
@endsection
