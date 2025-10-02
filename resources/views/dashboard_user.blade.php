@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Home
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col gap-6">
            <!-- Greeting Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-2">
                    <img 
                        src="{{ asset('images/greeting.svg') }}"
                        alt="Welcome! Bulusanon Employee"
                        class="w-full h-auto object-cover rounded-lg"
                    >
                </div>
            </div>
        </div>
    </div>
@endsection
