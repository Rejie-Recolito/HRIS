<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    public $notifications;
    public $unreadCount = 0;

    public function mount()
    {
        $user = Auth::user();
        $this->notifications = $user ? $user->notifications()->latest()->get() : collect();
        $this->unreadCount = $this->notifications->whereNull('read_at')->count();
    }

    public function markSingleAsRead($notificationId)
    {
        $user = Auth::user();
        if ($user) {
            $notification = $user->notifications()->where('id', $notificationId)->first();
            if ($notification && $notification->read_at === null) {
                $notification->markAsRead();
            }
            $this->notifications = $user->notifications()->latest()->get();
            $this->unreadCount = $this->notifications->whereNull('read_at')->count();
        }
    }

    public function deleteNotification($notificationId)
    {
        $user = Auth::user();
        if ($user) {
            $notification = $user->notifications()->where('id', $notificationId)->first();
            if ($notification) {
                $notification->delete();
            }
            $this->notifications = $user->notifications()->latest()->get();
            $this->unreadCount = $this->notifications->whereNull('read_at')->count();
        }
    }

    public function markNotificationsRead()
    {
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
            $this->notifications = $user->notifications()->latest()->get();
            $this->unreadCount = $this->notifications->whereNull('read_at')->count();
        }
    }

    public function render()
    {
        return view('livewire.notification-bell', [
            'notifications' => $this->notifications,
            'unreadCount' => $this->unreadCount,
        ]);
    }
}
