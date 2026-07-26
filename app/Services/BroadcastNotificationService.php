<?php

namespace App\Services;

use App\Models\BroadcastNotification;
use App\Jobs\SendBroadcastNotificationJob;


class BroadcastNotificationService
{
    public function sendBroadcastNotification($request): BroadcastNotification {
        $notification = BroadcastNotification::create([
            'title'=>$request->title,
            'body'=>$request->body,
            'type'=>$request->type,
            'data'=>$request->data

        ]);

        SendBroadcastNotificationJob::dispatch(
            $notification
        );

        return ['notification' => $notification , 'message' => 'notification sent successfully'];
    }

}