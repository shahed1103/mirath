<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\BroadcastNotification;
use App\Models\User;
use Carbon\Carbon;
use Auth;
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

    public function getNotifications(): array{
        $user = Auth::user();

        $personalNotifications = Notification::where('user_id', $user->id)
            ->get();


        $broadcastNotifications = collect();

        if ($user->hasRole('Client')) {
            $broadcastNotifications = BroadcastNotification::where(
                    'created_at',
                    '>=',
                    $user->created_at
                )
                ->get();
        }
        
        $notifications = $personalNotifications
            ->concat($broadcastNotifications)
            ->sortByDesc('created_at')
            ->values();

        return [
            'notifications' => $notifications,
            'message' => 'Notifications retrieved successfully.'
        ];
    }

    public function getUnreadCount(): array{
        $user = Auth::user();

        return [
            'unread_notifications_count' =>
                Notification::where('user_id', $user->id)
                    ->where('is_read', false)
                    ->count(),

            'message' => 'Unread notifications count retrieved successfully.'
        ];
    }

    public function markAllAsRead(): array{
        $user = Auth::user();

        Notification::where('user_id', $user->id)
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