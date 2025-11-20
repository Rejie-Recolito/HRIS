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
            <div class="p-4 border-b border-[#198f51] hover:bg-[#e3f9ec] dark:hover:bg-[#3c3c3c]">
                <p class="text-sm text-black dark:text-gray-300 whitespace-pre-line">
                    {{ $notification->data['message'] ?? 'No message available' }}
                </p>
                <small class="text-gray-500 dark:text-gray-400">
                    {{ $notification->created_at->diffForHumans() }}
                </small>
            </div>
        @endforeach
    @endif
</div>