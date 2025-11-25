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

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen w-full flex flex-col sm:justify-center bg-no-repeat bg-cover items-center pt-6 sm:pt-0 relative" style="background-image: url('/Images/lguhall.png');">
        <!-- Overlay backgrounds -->
        <div class="absolute inset-0 bg-[#198F51]/40"></div>
        <div class="absolute inset-0 bg-[#267E3F]/60"></div>
        <div class="z-50">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </div>
        <div class="w-full z-50 sm:max-w-md mt-6 mb-8 px-4 sm:px-6 py-4 bg-white dark:bg-white-800 shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>
</body>

</html>