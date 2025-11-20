<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AdminNotification extends Notification
{
    use Queueable;

    protected $serviceRecord;
    protected $leaveApplication;

    /**
     * Create a new notification instance.
     */
    public function __construct($serviceRecord, $leaveApplication)
    {
        $this->serviceRecord = $serviceRecord;
        $this->leaveApplication = $leaveApplication;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('New Service Record Submitted')
                    ->line('A new service record has been submitted.')
                    ->line('Name: ' . $this->serviceRecord->name)
                    ->line('Job Title: ' . $this->serviceRecord->job_title)
                    ->line('Date of Service: ' . $this->serviceRecord->date_of_service)
                    ->action('View Service Record', route('service_record.request_form', ['id' => $this->serviceRecord->id]))
                    ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->leaveApplication->user->name . ' has requested a leave form.',
            'leave_application_id' => $this->leaveApplication->id,
        ];
    }
}