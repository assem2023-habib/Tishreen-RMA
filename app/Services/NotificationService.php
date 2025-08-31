<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Notifications\SendNotification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class NotificationService
{
    public function createNotification(array $data, array $userIds = [])
    {
        $notification = Notification::create([
            'title' => $data['title'],
            'message' => $data['message'],
            'notification_type' => $data['notification_type'],
            'notification_priority' => $data['notification_priority'],
        ]);

        if (!empty($userIds)) {
            $notification->users()->attach($userIds);
        }

        return $notification->load('users');
    }

    public function getUserNotifications(User $user)
    {
        return $user->notifications()
            ->orderBy('notification_user.created_at', 'desc')
            ->get();
    }

    public function markAsRead(User $user, int $notificationId)
    {
        $notification = $user->notifications()
            ->where('notification_id', $notificationId)
            ->firstOrFail();

        $notification->pivot->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return $notification;
    }
    public function sendNotification(array $data, ?array $userIds = null)
    {
        if ($userIds) {
            $users = User::whereIn('id', $userIds)->get();
            NotificationFacade::send($users, new SendNotification($data));
        } else {
            $users = User::all();
            NotificationFacade::send($users, new SendNotification($data));
        }
    }
}
