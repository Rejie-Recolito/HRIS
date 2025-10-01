<div class="relative" x-data="{ open: false }">
    <button class="relative items-center" @click="open = !open">
        @foreach ($notifications as $notification)
            @php
                $notificationData = json_decode($notification['data'], true);
            @endphp
            <div class="p-4 border-b hover:bg-gray-100 ">
                <p class="text-sm text-gray-700">
                    {{ $notificationData['message'] ?? 'No message available' }}
                </p>
                <small class="text-gray-500">
                    {{ $notification->created_at->diffForHumans() }}
                </small>
            </div>
        @endforeach
    </button>
</div>