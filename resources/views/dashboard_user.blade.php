@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Dashboard
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col gap-6">
            <!-- Greeting Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-2xl font-bold text-center text-gray-900 dark:text-gray-100">
                    HUMAN RESOURCE INFORMATION SYSTEM
                </div>
            </div>
            <!-- Information Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 text-center">
                    Welcome to your dashboard!<br>
                    You're logged in!<br>
                    Here you can view your leave applications, service records, and more.
                </div>
            </div>
        </div>
    </div>
@endsection
