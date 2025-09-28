@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ $serviceRecord->name }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-2 gap-4">
                        <div>Age: {{ $serviceRecord->age }}</div>
                        <div>Salary: Php {{ number_format($serviceRecord->salary, 2) }}</div>
                        <div>Date of Birth: {{ $serviceRecord->date_of_birth }}</div>
                        <div>Job Title: {{ $serviceRecord->job_title }}</div>
                        <div>Place of Birth: {{ $serviceRecord->place_of_birth }}</div>
                        <div>Office: {{ $serviceRecord->office }}</div>
                        <div>Status: {{ $serviceRecord->status }}</div>
                        <div>Date of Service: {{ $serviceRecord->date_of_service }}</div>
                        <div>Place of Assignment: {{ $serviceRecord->place_of_assignment }}</div>
                    </div>
                    <div class="mt-4">
                        <form method="POST" action="{{ route('service_record.generate', ['id' => $serviceRecord->id]) }}">
                            @csrf
                            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md">Generate Service Record</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection