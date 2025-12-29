<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use App\Models\Notification as NotificationModel;
use Illuminate\Support\Str;

class PivotDatabaseChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $data = $notification->toArray($notifiable);
        
        // البحث عن رسالة مطابقة تماماً (العنوان والمحتوى) لتجنب تكرار الرسائل في جدول notifications
        $notificationRecord = NotificationModel::firstOrCreate(
            [
                'title' => $data['title'] ?? null,
                'message' => $data['body'] ?? $data['message'] ?? null,
                'notification_type' => $data['type'] ?? 'general',
            ],
            [
                'id' => (string) Str::uuid(),
                'notification_priority' => $data['priority'] ?? 'medium',
            ]
        );

        // ربط الرسالة بالمستخدم في الجدول الوسيط
        $notifiable->customNotifications()->syncWithoutDetaching([
            $notificationRecord->id => [
                'notifiable_type' => get_class($notifiable),
                'notifiable_id' => $notifiable->id,
                'data' => json_encode($data),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
