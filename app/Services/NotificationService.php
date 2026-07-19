<?php

namespace App\Services;

use App\Models\UserDevice;
use App\Models\Notification;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;
use Exception;

class NotificationService
{

    public function deviceFcmToken($request): array {
        $user = auth()->user();

        if (!$user) {
            throw new Exception("User not found", 404);
        }
        
        UserDevice::updateOrCreate(
            [
                'user_id' => $user->id,
                'fcm_token' => $request->fcm_token,
            ],
            [
                'device_type' => $request->device_type,
            ]
        );

        return ['data' => null , 'message' => 'Device token updated successfully'];

    }

    public function getUserNotifications($userId): array {
   Notification::where('user_id', $userId)
        ->where('read_at', false)
        ->update(['read_at' => true]);

    $notifications = Notification::where('user_id', $userId)
        ->orderBy('created_at', 'desc')
        ->get();

        $message = "all notifications retrived successfully";

    return ['notifications' => $notifications  , 'message' => $message];
    }

    public function getUnreadCount($userId): array {
        $count = Notification::where('user_id', $userId)
            ->where('read_at', false)
            ->count();

            $message = "count of unread notifications retrived successfully";

            return ['unread_notifications_count' => $count  , 'message' => $message];

    }

}