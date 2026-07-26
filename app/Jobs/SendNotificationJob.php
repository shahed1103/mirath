<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Services\FirebaseService;
use App\Services\DeviceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendNotificationJob implements ShouldQueue
{
    use Queueable;

    public $tries = 5;
    public $backoff = 30;


    public function __construct(
        public Notification $notification
    ) {}

    public function handle(FirebaseService $firebaseService , DeviceService $deviceService): void {

        $devices = $deviceService ->getUserDevices($this->notification->user_id);

        $firebaseService->sendToDevices(
            $devices,
            $this->notification->title,
            $this->notification->body,
            $this->notification->type,
            $this->notification->data ?? []
        );

        event(new \App\Events\NotificationSent(
            $this->notification
        ));

}
}