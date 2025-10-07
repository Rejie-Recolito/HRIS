@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white dark:text-white leading-tight">
        LEAVE APPLICATION
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @livewire('leave-applications-table')
                </div>
            </div>
        </div>
    </div>
@endsection
