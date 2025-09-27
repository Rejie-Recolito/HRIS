@extends('layouts.app')

    @section('header')
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Employee') }}
        </h2>
    @endsection

    @section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('employees.update', $employee->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-200">Name</label>
                            <input type="text" name="name" id="name" value="{{ $employee->name }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-gray-900">
                        </div>

                        <div class="mb-4">
                            <label for="department" class="block text-sm font-medium text-gray-200">Department</label>
                            <input type="text" name="department" id="department" value="{{ $employee->department }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-gray-950">
                        </div>

                        <div class="mb-4">
                            <label for="job_title" class="block text-sm font-medium text-gray-200">Job Title</label>
                            <input type="text" name="job_title" id="job_title" value="{{ $employee->job_title }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-gray-900">
                        </div>

                        <div class="mb-4">
                            <label for="start_date" class="block text-sm font-medium text-gray-200">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $employee->start_date }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-gray-900">
                        </div>

                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-200">Status</label>
                            <input type="text" name="status" id="status" value="{{ $employee->status }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-gray-900">
                        </div>

                        <div class="mb-4">
                            <label for="sex" class="block text-sm font-medium text-gray-200">Sex</label>
                            <input type="text" name="sex" id="sex" value="{{ $employee->sex }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-gray-900">
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endsection