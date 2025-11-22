@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-sm sm:text-xl text-white dark:text-white leading-tight flex items-center whitespace-nowrap">
        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        HOME
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col gap-6">
            <!-- Greeting Card -->
            <div class="overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-2">


                    @if(file_exists(public_path('Images/greeting-mobile.png')))
                        @php
                            $mobileImage = base64_encode(file_get_contents(public_path('Images/greeting-mobile.png')));
                            $tabletImage = base64_encode(file_get_contents(public_path('Images/greeting-tablet.png')));
                            $desktopImage = base64_encode(file_get_contents(public_path('Images/greeting-desktop.png')));
                        @endphp
                        
                        <picture>
                            <source media="(min-width: 1024px)" srcset="data:image/png;base64,{{ $desktopImage }}">
                            <source media="(min-width: 768px)" srcset="data:image/png;base64,{{ $tabletImage }}">
                            <img src="data:image/png;base64,{{ $mobileImage }}" 
                                 alt="Welcome! Bulusanon Employee"
                                 class="w-full h-auto object-cover rounded-lg">
                        </picture>
                    @else
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <strong>Error:</strong> Greeting images not found in public/Images/ directory.
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
