@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Service Record') }}
    </h2>
@endsection

@section('content')
<div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h2 class="text-xl font-bold mb-4">Service Record Form</h2>

                    <form method="POST" action="{{ route('service-records.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label for="age" class="block text-sm font-medium text-gray-700">Age</label>
                            <input type="number" name="age" id="age" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label for="salary" class="block text-sm font-medium text-gray-700">Salary</label>
                            <input type="number" step="0.01" name="salary" id="salary" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label for="job_title" class="block text-sm font-medium text-gray-700">Job Title</label>
                            <input type="text" name="job_title" id="job_title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label for="place_of_birth" class="block text-sm font-medium text-gray-700">Place of Birth</label>
                            <input type="text" name="place_of_birth" id="place_of_birth" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label for="office" class="block text-sm font-medium text-gray-700">Office</label>
                            <input type="text" name="office" id="office" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <input type="text" name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label for="date_of_service" class="block text-sm font-medium text-gray-700">Date of Service</label>
                            <input type="date" name="date_of_service" id="date_of_service" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label for="place_of_assignment" class="block text-sm font-medium text-gray-700">Place of Assignment</label>
                            <input type="text" name="place_of_assignment" id="place_of_assignment" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection