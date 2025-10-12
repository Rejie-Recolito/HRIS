@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white dark:text-white leading-tight flex items-center">
        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ __('PROFILE') }}
    </h2>
@endsection
    
@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- Header Section -->
        <div class="text-center mb-8 bg-white dark:bg-[#1c1c1d] rounded-lg border border-gray-700 dark:border-gray-600">
            <div class="flex items-center justify-center mb-3">
                <svg class="w-7 h-7 sm:w-9 sm:h-9 mr-2 sm:mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h1 class="font-bold custom-label custom-heading mb-0" style="margin-top: 0 !important;">Account Settings</h1>
            </div>
            <p class="text-base sm:text-lg text-gray-600 dark:text-gray-300 mb-2">Manage your profile information and account settings.</p>
            <div class="w-24 h-1 bg-green-600 mx-auto rounded"></div>
        </div>

        <!-- Profile Picture Section -->
        <div class="bg-white dark:bg-[#1c1c1d] overflow-hidden shadow-sm sm:rounded-lg border-2" style="border-color: #2bb16b;">
            <div class="p-4 sm:p-6">
                @include('profile.partials.add-profile-picture')
            </div>
        </div>

        <!-- Profile Information Section -->
        <div class="bg-white dark:bg-[#1c1c1d] overflow-hidden shadow-sm sm:rounded-lg border-2" style="border-color: #2bb16b;">
            <div class="p-4 sm:p-6">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Password Section -->
        <div class="bg-white dark:bg-[#1c1c1d] overflow-hidden shadow-sm sm:rounded-lg border-2" style="border-color: #2bb16b;">
            <div class="p-4 sm:p-6">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Delete Account Section -->
        <div class="bg-white dark:bg-[#1c1c1d] overflow-hidden shadow-sm sm:rounded-lg border-2" style="border-color: #2bb16b;">
            <div class="p-4 sm:p-6">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection
