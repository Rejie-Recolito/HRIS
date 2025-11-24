@php
    if (!isset($notifications)) {
        $notifications = collect();
    }
@endphp
<div class="relative">
    <div class="py-2 p-2 border-b border-[#198f51] bg-gray-50 dark:bg-[#282828]">
        <div class="p-2 border-b border-[#198f51] bg-[#198f51]">
            <h3 class="text-md font-semibold text-white dark:text-gray-200">NOTIFICATIONS</h3>
        </div>
    </div>
    @if($notifications->isEmpty())
        <div class="p-4 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">No notifications</p>
        </div>
    @else
        @foreach ($notifications as $notification)
            <div class="p-4 border-b border-[#198f51] hover:bg-[#e3f9ec] dark:hover:bg-[#3c3c3c] relative group">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <p class="text-sm text-black dark:text-gray-300 whitespace-pre-line">
                            {{ $notification->data['message'] ?? 'No message available' }}
                        </p>
                    </div>
                    <!-- Three-dot menu -->
                    <div x-data="{ open: false }" class="relative ms-2">
                        <button @click="open = !open" class="p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none">
                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><circle cx="4" cy="10" r="2"/><circle cx="10" cy="10" r="2"/><circle cx="16" cy="10" r="2"/></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-2 w-32 bg-white dark:bg-[#222] rounded shadow-lg z-30 border border-gray-200 dark:border-gray-700">
                            <button wire:click="markSingleAsRead('{{ $notification->id }}')" @click="open = false" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Mark as Read
                            </button>
                            <button wire:click="deleteNotification('{{ $notification->id }}')" @click="open = false" class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mt-1">
                    <small class="text-gray-500 dark:text-gray-400 block">
                        {{ $notification->created_at->format('F j, Y h:i A') }}
                    </small>
                    <small class="text-gray-400 dark:text-gray-500 block sm:ml-2">
                        ({{ $notification->created_at->diffForHumans() }})
                    </small>
                </div>
            </div>
        @endforeach
    @endif
</div>