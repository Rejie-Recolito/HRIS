<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ServiceRecordReady extends Notification implements ShouldQueue
{
    use Queueable;

    protected $serviceRecordRequestId;

    public function __construct($serviceRecordRequestId)
    {
        $this->serviceRecordRequestId = $serviceRecordRequestId;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Your requested Certified True Copy of Service Record is ready to be claimed physically at the HR Office.',
            'service_record_request_id' => $this->serviceRecordRequestId,
        ];
    }
}
