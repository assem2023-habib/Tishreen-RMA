<?php

namespace App\Listeners;

use App\Events\NotificationSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendNotificationListener implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'notifications';

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NotificationSent $event): void
    {
        try {
            // تسجيل الإشعار
            Log::info('Notification sent via listener', [
                'notification_id' => $event->notification->id,
                'user_id' => $event->user->id,
                'type' => $event->notification->notification_type,
                'priority' => $event->notification->notification_priority,
            ]);

            // هنا يمكنك إضافة منطق إضافي مثل:
            // - إرسال push notifications
            // - إرسال SMS
            // - إرسال email
            // - تحديث Firebase Cloud Messaging

        } catch (\Exception $e) {
            Log::error('Failed to process notification in listener', [
                'error' => $e->getMessage(),
                'notification_id' => $event->notification->id ?? 'unknown',
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(NotificationSent $event, \Throwable $exception): void
    {
        Log::error('Notification listener failed', [
            'notification_id' => $event->notification->id ?? 'unknown',
            'user_id' => $event->user->id ?? 'unknown',
            'error' => $exception->getMessage(),
        ]);
    }
}
