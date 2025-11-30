@if(Auth::user() && Auth::user()->is_admin)
    <!-- Admin Notification Bell -->
    <div class="relative ms-4" x-data="{ openNotifications: false }">
        <button @click="openNotifications = !openNotifications; if(openNotifications){ $wire.markNotificationsRead() }" class="relative z-10 flex items-center justify-center h-8 w-8 rounded-full bg-white dark:bg-[#282828] focus:outline-none">
            <svg class="h-5 w-5 text-gray-600 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            @if($unreadCount > 0)
                <span x-show="!openNotifications" class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-xs font-bold bg-red-600 text-white animate-pulse min-w-[20px] min-h-[20px]">{{ $unreadCount }}</span>
            @endif
        </button>
        <div x-show="openNotifications" @click.outside="openNotifications = false" class="absolute right-0 mt-2 w-[480px] max-h-[600px] overflow-y-auto bg-white dark:bg-[#282828] rounded-lg shadow-lg overflow-hidden z-20" style="display: none;">
            <div class="flex justify-end p-2 bg-gray-50 dark:bg-[#222] border-b border-[#198f51]">
                @if($unreadCount > 0)
                    <button wire:click="markNotificationsRead" class="text-xs px-3 py-1 rounded bg-[#198f51] text-white hover:bg-[#157a43] transition">Mark all as Read</button>
                @endif
            </div>
            <x-notification-component :notifications="$this->notifications" />
        </div>
    </div>
@else
    <!-- User Notification Bell -->
    <div class="relative ms-4" x-data="{ openNotifications: false }">
        <button @click="openNotifications = !openNotifications; if(openNotifications){ $wire.markNotificationsRead() }" class="relative z-10 flex items-center justify-center h-8 w-8 rounded-full bg-white dark:bg-[#282828] focus:outline-none">
            <svg class="h-5 w-5 text-gray-600 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            @if($unreadCount > 0)
                <span x-show="!openNotifications" class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-xs font-bold bg-red-600 text-white animate-pulse min-w-[20px] min-h-[20px]">{{ $unreadCount }}</span>
            @endif
        </button>
        <div x-show="openNotifications" @click.outside="openNotifications = false" class="absolute right-0 mt-2 w-[480px] max-h-[600px] overflow-y-auto bg-white dark:bg-[#282828] rounded-lg shadow-lg overflow-hidden z-20" style="display: none;">
            <div class="flex justify-end p-2 bg-gray-50 dark:bg-[#222] border-b border-[#198f51]">
                @if($unreadCount > 0)
                    <button wire:click="markNotificationsRead" class="text-xs px-3 py-1 rounded bg-[#198f51] text-white hover:bg-[#157a43] transition">Mark all as Read</button>
                @endif
            </div>
            <x-notification-component :notifications="$this->notifications" wire:on="markSingleAsRead,deleteNotification" />
        </div>
    </div>
@endif
