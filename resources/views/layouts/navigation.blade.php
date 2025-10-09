<nav x-data="{ 
    open: false, 
    openUserNotifications: false,
    isDark: localStorage.getItem('darkMode') === 'true' || false,
    init() {
        this.updateDarkMode();
    },
    toggleDarkMode() {
        this.isDark = !this.isDark;
        localStorage.setItem('darkMode', this.isDark);
        this.updateDarkMode();
    },
    updateDarkMode() {
        if (this.isDark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
}" class="bg-[#198f51] dark:bg-[#198F51] border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
            @if(Auth::user() && Auth::user()->is_admin)
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="h-9 w-9">
                        <x-application-logo class="block  fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>
            @else
            <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="h-9 w-9">
                        <x-application-logo class="block  fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>
            @endif
                <!-- Navigation Links -->

                    @if(Auth::user() && Auth::user()->is_admin)
                        <!-- Admin Navigation -->
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                {{ __('DASHBOARD') }}
                            </x-nav-link>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.index')">
                                {{ __('EMPLOYEES') }}
                            </x-nav-link>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('service_record')" :active="request()->routeIs('service_record')">
                                {{ __('SERVICE RECORD') }}
                            </x-nav-link>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('leave')" :active="request()->routeIs('leave')">
                                {{ __('LEAVE') }}
                            </x-nav-link>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('dtr')" :active="request()->routeIs('dtr')">
                                {{ __('DTR') }}
                            </x-nav-link>
                        </div>
                    @else
                        <!-- User Navigation -->
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                                {{ __('HOME') }}
                            </x-nav-link>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('leave.user')" :active="request()->routeIs('leave.user')">
                                {{ __('LEAVE') }}
                            </x-nav-link>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('service_record.user')" :active="request()->routeIs('service_record.user')">
                                {{ __('SERVICE RECORD') }}
                            </x-nav-link>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('add-user-information.user')" :active="request()->routeIs('add-user-information.user')">
                                {{ __('EMPLOYEE PROFILE') }}
                            </x-nav-link>
                        </div>
                    @endif



            </div>
            
            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Dark Mode Toggle -->
                <button @click="toggleDarkMode()" class="me-3 p-2 rounded-md text-white hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 transition duration-150 ease-in-out">
                    <svg x-show="!isDark" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                    <svg x-show="isDark" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </button>
                
                @if(Auth::user() && Auth::user()->profile_picture)
                    <img src="{{ asset('storage/profile_pictures/' . Auth::user()->profile_picture) }}" alt="Profile Picture" class="h-8 w-8 rounded-full object-cover me-2">
                @endif
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user() ? Auth::user()->name : 'Guest' }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>

                @if(Auth::user() && Auth::user()->is_admin)
                    <!-- Admin Notification Bell -->
                    <div class="relative ms-4">
                        <button @click="open = !open" class="relative z-10 block h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 focus:outline-none">
                            <svg class="h-6 w-6 text-gray-600 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-2 w-64 h-[380px] overflow-y-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden z-20">
                            <x-notification-component :notifications="$notifications" />
                        </div>
                    </div>
                @else
                    <!-- User Notification Bell -->
                    <div class="relative ms-4">
                        <button @click="openUserNotifications = !openUserNotifications" class="relative z-10 block h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 focus:outline-none">
                            <svg class="h-6 w-6 text-gray-600 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </button>
                        <div x-show="openUserNotifications" @click.outside="openUserNotifications = false" class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden z-20">
                            <x-notification-component :notifications="$notifications" />
                            
                        </div>
                    </div>
                @endif
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                @if(Auth::user() && Auth::user()->profile_picture)
                    <img src="{{ asset('storage/profile_pictures/' . Auth::user()->profile_picture) }}" alt="Profile Picture" class="h-8 w-8 rounded-full object-cover me-2">
                @endif
                
                <div class="relative">
                    <button @click="openResponsiveNotifications = !openResponsiveNotifications" class="relative z-10 block h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 focus:outline-none me-2">
                        <svg class="h-6 w-6 text-gray-600 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </button>
                    <div x-show="openResponsiveNotifications" @click.outside="openResponsiveNotifications = false" class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden z-20">
                        <x-notification-component :notifications="$notifications" />
                    </div>
                </div>

                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white dark:text-white hover:text-white dark:hover:text-white hover:bg-green-600 dark:hover:bg-green-600 focus:outline-none focus:bg-green-600 dark:focus:bg-green-600 focus:text-white dark:focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu full screen overlay - completely covers content -->
    <div x-show="open" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-[#198f51] dark:bg-black z-50 sm:hidden flex flex-col"
         style="display: none;">
         
        <!-- Top container with visibility toggle and close button -->
        <div class="bg-[#198f51] dark:bg-[#198f51]">
            <div class="flex justify-between items-center p-4">
                <!-- Dark Mode Toggle at top left -->
                <button @click="toggleDarkMode()" class="flex items-center p-2 rounded-md text-white hover:text-gray-200 hover:bg-green-600 focus:outline-none focus:bg-green-600 transition duration-150 ease-in-out">
                <svg x-show="!isDark" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
                <svg x-show="isDark" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span class="text-sm" x-text="isDark ? 'Light' : 'Dark'"></span>
            </button>
            
                <!-- Close button at top right -->
                <button @click="open = false" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-gray-200 hover:bg-green-600 focus:outline-none focus:bg-green-600 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>        <!-- Menu content inside the overlay -->
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('service_record.user')" :active="request()->routeIs('service_record.user')">
                {{ __('Service Record') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('leave.user')" :active="request()->routeIs('leave.user')">
                {{ __('Leave') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-white dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-white dark:text-gray-200">{{ Auth::user() ? Auth::user()->name : 'Guest' }}</div>
                <div class="font-medium text-sm text-white dark:text-gray-400">{{ Auth::user() ? Auth::user()->email : '' }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        
        <div class="flex-1" @click="open = false"></div>
    </div>

</nav>
