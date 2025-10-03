
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
