<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @endif
</head>

<body class="bg-[#FDFDFC] dark:bg-[#198F51] text-[#1b1b18] min-h-screen flex flex-col items-center">
    <!-- Header fixed at top -->
    <header class="w-full fixed top-0 left-0 z-50 bg-[#FDFDFC] dark:bg-[#198F51] px-6 lg:px-8 py-4">
        @if (Route::has('login'))
        <nav class="flex items-center justify-end gap-4 max-w-7xl mx-auto">
            @auth
            <a href="{{ url('/dashboard') }}"
                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                Dashboard
            </a>
            @else
            <a href="{{ route('login') }}"
                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                Log in
            </a>
            @if (Route::has('register'))
            <a href="{{ route('register') }}"
                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                Register
            </a>
            @endif
            @endauth
        </nav>
        @endif
    </header>

    <!-- Spacer to offset fixed header -->
    <div class="h-24"></div>

    <!-- Centered Logo and Title -->
    <main class="flex flex-col items-center text-center px-5 mt-10">
        <img src="/Images/logo.jpg" alt="LGU Bulusan Logo"
            class="w-[225px] h-[224px] rounded-full mb-4" />

        <h1 class="text-4xl lg:text-5xl font-extrabold text-white mb-2">
            LGU Bulusan Service Record Portal
        </h1>

        <p class="text-base lg:text-lg text-white">
            Welcome to the Human Resource Information System
        </p>
    </main>
</body>

</html>