<?php

namespace App\Services;

use App\Jobs\SendNotificationJob;

class NotificationManager
{
    public function __construct(
        private NotificationService $notificationService,
        private FirebaseService $firebaseService

    ) {}

    public function sendNotification($request):  array{
        $notification = $this->notificationService->createNotification([
                    'user_id'=>$request->userId,
                    'title'=>$request->title,
                    'body'=>$request->body,
                    'type'=>$request->type,
                    'data'=>$request->data
            ]);
    
        SendNotificationJob::dispatch($notification);

        return ['notification' => $notification , 'message' => 'Notification sent successfully.'];
    }

}