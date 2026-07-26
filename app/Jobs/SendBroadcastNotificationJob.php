<?php

namespace App\Jobs;

use App\Models\BroadcastNotification;
use App\Services\FirebaseService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendBroadcastNotificationJob implements ShouldQueue
{
    use Queueable;


    public $tries = 5;

    public $backoff = 30;


    public function __construct(
        public BroadcastNotification $notification
    ) {
    }


    public function handle(
        FirebaseService $firebaseService
    ): void {


        $firebaseService->sendToTopic(
            'all_users',
            $this->notification->title,
            $this->notification->body,
            $this->notification->type,
            $this->notification->data ?? []
        );

        event(new \App\Events\BroadcastNotificationSent($this->notification));
    }
}