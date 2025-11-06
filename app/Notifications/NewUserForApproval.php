<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserForApproval extends Notification
{
    use Queueable;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New user awaiting approval')
            ->line('A new user has registered and is awaiting approval: ' . $this->user->name)
            ->action('View users', url('/admin/users'));
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->user->name . ' has registered and is awaiting approval.',
            'user_id' => $this->user->id,
        ];
    }
}
