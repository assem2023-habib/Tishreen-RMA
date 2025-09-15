<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Notifications\SendNotification;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    public function getUserNotifications(User $user, int $limit = 50, bool $unreadOnly = false)
    {
        $query = $user->notifications()
            ->orderBy('notification_user.created_at', 'desc');

        if ($unreadOnly) {
            $query->wherePivot('is_read', false);
        }

        return $query->limit($limit)->get();
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

    public function markAllAsRead(User $user)
    {
        return $user->notifications()
            ->wherePivot('is_read', false)
            ->updateExistingPivot($user->id, [
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    public function sendNotification(array $data, ?array $userIds = null)
    {
        try {
            // إنشاء الإشعار في قاعدة البيانات
            $notification = $this->createNotification($data, $userIds ?? []);

            // إرسال الإشعارات المباشرة
            if ($userIds) {
                $users = User::whereIn('id', $userIds)->get();
                NotificationFacade::send($users, new SendNotification($data));
            } else {
                $users = User::all();
                NotificationFacade::send($users, new SendNotification($data));
            }

            // تسجيل الإشعار
            Log::info('Notification sent successfully', [
                'notification_id' => $notification->id,
                'users_count' => count($userIds ?? []),
                'type' => $data['notification_type'] ?? 'unknown'
            ]);

            return $notification;
        } catch (\Exception $e) {
            Log::error('Failed to send notification', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function getUnreadCount(User $user)
    {
        return $user->notifications()
            ->wherePivot('is_read', false)
            ->count();
    }

    public function deleteNotification(int $notificationId, User $user)
    {
        $notification = $user->notifications()
            ->where('notification_id', $notificationId)
            ->firstOrFail();

        // حذف العلاقة فقط (لا حذف الإشعار نفسه)
        $notification->pivot->delete();

        return true;
    }

    public function getNotificationStats(User $user)
    {
        $stats = DB::table('notification_user')
            ->where('user_id', $user->id)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) as read_count,
                SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread_count
            ')
            ->first();

        return [
            'total' => $stats->total ?? 0,
            'read' => $stats->read_count ?? 0,
            'unread' => $stats->unread_count ?? 0,
        ];
    }

    public function sendBulkNotifications(array $notifications, array $userIds)
    {
        $results = [];

        foreach ($notifications as $notificationData) {
            try {
                $notification = $this->sendNotification($notificationData, $userIds);
                $results[] = [
                    'success' => true,
                    'notification_id' => $notification->id,
                    'title' => $notification->title
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'title' => $notificationData['title'] ?? 'Unknown'
                ];
            }
        }

        return $results;
    }
}
