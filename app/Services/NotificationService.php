<?php

namespace App\Services;

use App\Models\Notification;
use Carbon\Carbon;

class NotificationService
{
    public function createNotification(array $data): Notification {
        return Notification::create([
            'user_id' => $data['user_id'],
            'title'   => $data['title'],
            'body'    => $data['body'],
            'type'    => $data['type'],
            'data'    => $data['data'] ?? null,
        ]);
    }

    public function getNotifications(int $userId): array{
        $notifications = Notification::where('user_id', $userId)
            ->latest()
            ->get();

        return [
            'notifications' => $notifications,
            'message' => 'Notifications retrieved successfully.'
        ];
    }

    public function getUnreadCount(int $userId): array{
        return [
            'unread_notifications_count' =>
                Notification::where('user_id', $userId)
                    ->where('is_read', false)
                    ->count(),

            'message' => 'Unread notifications count retrieved successfully.'
        ];
    }

    public function markAllAsRead(int $userId): array{
        Notification::where('user_id', $userId)
            ->where('is_read' , false)
            ->update([
                'is_read' => true
            ]);

        return [
            'data' => null,
            'message' => 'Notifications marked as read.'
        ];
    }
}