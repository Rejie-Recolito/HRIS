@extends('layouts.app')

@section('header')
        <div class="flex items-center">
            <a href="{{ route('employees.index') }}" class="mr-4 text-gray-800 dark:text-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Edit Employee') }}
            </h2>
        </div>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div x-data="{ showOverlay: false, submitForm() { this.$refs.editEmployeeForm.submit(); } }">
                    <div x-show="showOverlay" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50">
                        <div class="bg-white p-6 rounded shadow-lg text-center">
                            <h2 class="text-lg font-semibold text-green-600 mb-4">Employee details updated</h2>
                            <button @click="submitForm" class="bg-blue-600 text-white px-4 py-2 rounded">OK</button>
                        </div>
                    </div>

                    <form x-ref="editEmployeeForm" method="POST" action="{{ route('employees.update', $employee->id) }}" @submit.prevent="showOverlay = true">
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
                            <select name="sex" id="sex" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-gray-900">
                                <option value="Male" {{ $employee->sex === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ $employee->sex === 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>

                        <div class="flex justify-between">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection