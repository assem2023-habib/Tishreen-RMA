<?php

namespace App\Support;

use Filament\Notifications\Notification as FilamentNotification;
use App\Models\Notification as NotificationModel;
use Illuminate\Support\Str;

class SharedNotification extends FilamentNotification
{
    public function sendToDatabase($notifiable, bool $isStorable = true): static
    {
        if (! $notifiable) {
            return $this;
        }

        $data = $this->toArray();
        
        // البحث عن رسالة مطابقة لتجنب التكرار
        $notificationRecord = NotificationModel::firstOrCreate(
            [
                'title' => $data['title'] ?? null,
                'message' => $data['body'] ?? null,
                'notification_type' => $data['status'] ?? 'info',
            ],
            [
                'id' => (string) Str::uuid(),
                'notification_priority' => 'medium',
            ]
        );

        // ربط في الجدول الوسيط
        $notifiable->notifications()->syncWithoutDetaching([
            $notificationRecord->id => [
                'data' => json_encode($data),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        return $this;
    }
}
