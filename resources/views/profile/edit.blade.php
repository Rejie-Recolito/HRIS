@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-white dark:text-white leading-tight">
        {{ __('Profile') }}
    </h2>
@endsection
    
    @section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-[#1c1c1d] shadow sm:rounded-lg profile-border">
                <div class="max-w-xl">
                    @include('profile.partials.add-profile-picture')
                </div>
             </div>
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg profile-border">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-[#1c1c1d] shadow sm:rounded-lg profile-border">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-[#1c1c1d] shadow sm:rounded-lg profile-border">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>


        </div>
    </div>
    @endsection
