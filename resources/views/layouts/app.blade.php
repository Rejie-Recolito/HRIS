<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LGU-Bulusan Employee Portal') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Favicon - updated to match welcome page -->
        <link rel="icon" type="image/jpeg" href="{{ asset('Images/logo.jpg') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        <!-- Alpine.js -->
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-[#282828]">
            @include('layouts.navigation')


            <!-- Page Heading -->
            <div class="pt-16">
                @hasSection('header')
                    <header class="bg-[#198f51] text-white dark:bg-[#198F51] shadow mb-6">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            <div class="flex items-center gap-4">
                                <!-- Navigation Arrows -->
                                <div class="flex items-center gap-2">
                                    <button onclick="history.back()" class="p-1 hover:bg-white hover:bg-opacity-20 rounded transition-colors" title="Go Back">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                                        </svg>
                                    </button>
                                    <button onclick="history.forward()" class="p-1 hover:bg-white hover:bg-opacity-20 rounded transition-colors" title="Go Forward">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                        </svg>
                                    </button>
                                </div>
                                <!-- Header Content -->
                                <div class="flex-1">
                                    @yield('header')
                                </div>
                            </div>
                        </div>
                    </header>
                @endif
                <!-- Page Content -->
                <main>
                    @yield('content')
                </main>
            </div>
        </div>
        @livewireScripts
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </body>
</html>
