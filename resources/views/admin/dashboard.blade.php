@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-4 text-gray-900 dark:text-white flex items-center">
        <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Admin Dashboard
    </h1>
    <p class="text-gray-700 dark:text-gray-300">Welcome, Admin! This is your dashboard.</p>

    <div class="grid grid-cols-2 gap-4 mt-8">
        <!-- Total Employees Widget -->
        <div class="bg-green-500 text-white p-6 rounded-lg shadow-md flex items-center justify-between">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5m6-8a4 4 0 100-8 4 4 0 000 8z" />
                </svg>
                <span class="ml-4 text-lg font-bold">Total Employees</span>
            </div>
            <span class="text-2xl font-bold">{{ $totalEmployees }}</span>
        </div>

        <!-- Leave Applications Widget -->
        <div class="bg-green-500 text-white p-6 rounded-lg shadow-md flex items-center justify-between">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-6 4h6m-6 4h6m-6 4h6m-6 4h6" />
                </svg>
                <span class="ml-4 text-lg font-bold">Leave Applications</span>
            </div>
            <span class="text-2xl font-bold">{{ $leaveApplications }}</span>
        </div>

        <!-- Service Record Requests Widget -->
        <div class="bg-green-500 text-white p-6 rounded-lg shadow-md flex items-center justify-between">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="ml-4 text-lg font-bold">Service Record Requests</span>
            </div>
            <span class="text-2xl font-bold">{{ $serviceRecordRequests }}</span>
        </div>
    </div>
</div>
@endsection
