<?php

namespace App\Observers;

use App\Models\Notification;
use App\Services\NotificationService;

class NotificationObserver
{
    /**
     * Handle the Notification "created" event.
     */
    public function created(Notification $notification): void
    {
        // جلب المستخدمين المرتبطين (جدول notification_user)
        $users = $notification->users()->get();
        if ($users->isNotEmpty()) {
            app(NotificationService::class)->sendNotification($notification->toArray(), $users->pluck('id')->toArray());
        }
    }

    /**
     * Handle the Notification "updated" event.
     */
    public function updated(Notification $notification): void
    {
        //
    }

    /**
     * Handle the Notification "deleted" event.
     */
    public function deleted(Notification $notification): void
    {
        //
    }

    /**
     * Handle the Notification "restored" event.
     */
    public function restored(Notification $notification): void
    {
        //
    }

    /**
     * Handle the Notification "force deleted" event.
     */
    public function forceDeleted(Notification $notification): void
    {
        //
    }
}
