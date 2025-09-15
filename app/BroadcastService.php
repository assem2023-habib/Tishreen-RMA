<?php

namespace App;

use App\Events\NotificationSent;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class BroadcastService
{
    /**
     * إرسال إشعار مباشر
     */
    public static function sendNotification(Notification $notification, User $user, array $data = [])
    {
        try {
            // إرسال Event للبث المباشر
            event(new NotificationSent($notification, $user, $data));

            Log::info('Notification broadcasted successfully', [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'type' => $notification->notification_type
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to broadcast notification', [
                'error' => $e->getMessage(),
                'notification_id' => $notification->id ?? 'unknown',
                'user_id' => $user->id ?? 'unknown'
            ]);

            return false;
        }
    }

    /**
     * إرسال إشعارات لعدة مستخدمين
     */
    public static function sendToMultipleUsers(Notification $notification, array $userIds, array $data = [])
    {
        $users = User::whereIn('id', $userIds)->get();
        $successCount = 0;

        foreach ($users as $user) {
            if (self::sendNotification($notification, $user, $data)) {
                $successCount++;
            }
        }

        Log::info('Bulk notification broadcast completed', [
            'notification_id' => $notification->id,
            'total_users' => count($userIds),
            'successful_broadcasts' => $successCount
        ]);

        return $successCount;
    }

    /**
     * إرسال إشعار لجميع المستخدمين
     */
    public static function sendToAllUsers(Notification $notification, array $data = [])
    {
        $users = User::all();
        $successCount = 0;

        foreach ($users as $user) {
            if (self::sendNotification($notification, $user, $data)) {
                $successCount++;
            }
        }

        Log::info('Global notification broadcast completed', [
            'notification_id' => $notification->id,
            'total_users' => $users->count(),
            'successful_broadcasts' => $successCount
        ]);

        return $successCount;
    }

    /**
     * اختبار الاتصال
     */
    public static function testConnection(User $user)
    {
        try {
            // إرسال إشعار تجريبي
            $testNotification = new Notification([
                'title' => 'اختبار الاتصال',
                'message' => 'هذا إشعار تجريبي لاختبار الاتصال',
                'notification_type' => 'info',
                'notification_priority' => 'Reminder'
            ]);

            $result = self::sendNotification($testNotification, $user, ['test' => true]);

            return [
                'success' => $result,
                'message' => $result ? 'Connection test successful' : 'Connection test failed',
                'user_id' => $user->id,
                'timestamp' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage(),
                'user_id' => $user->id,
                'timestamp' => now()->toISOString()
            ];
        }
    }

    /**
     * الحصول على حالة الاتصال
     */
    public static function getConnectionStatus()
    {
        try {
            // التحقق من إعدادات Broadcasting
            $config = config('broadcasting.default');
            $connection = config("broadcasting.connections.{$config}");

            return [
                'status' => 'connected',
                'driver' => $config,
                'connection' => $connection,
                'timestamp' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'disconnected',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ];
        }
    }
}
