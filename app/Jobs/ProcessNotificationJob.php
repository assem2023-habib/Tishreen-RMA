<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120; // 2 minutes
    public $tries = 3;
    public $maxExceptions = 3;

    protected $notificationData;
    protected $userIds;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(array $notificationData, array $userIds = [], ?int $userId = null)
    {
        $this->notificationData = $notificationData;
        $this->userIds = $userIds;
        $this->userId = $userId;
        $this->queue = 'notifications';
    }

    /**
     * Execute the job.
     */
    public function handle(NotificationService $notificationService): void
    {
        try {
            Log::info('Processing notification job', [
                'notification_data' => $this->notificationData,
                'user_ids' => $this->userIds,
                'user_id' => $this->userId
            ]);

            // إنشاء الإشعار
            $notification = $notificationService->createNotification(
                $this->notificationData,
                $this->userIds
            );

            // إرسال الإشعارات
            if (!empty($this->userIds)) {
                $notificationService->sendNotification($this->notificationData, $this->userIds);
            } else {
                $notificationService->sendNotification($this->notificationData);
            }

            Log::info('Notification job completed successfully', [
                'notification_id' => $notification->id,
                'users_count' => count($this->userIds)
            ]);

        } catch (\Exception $e) {
            Log::error('Notification job failed', [
                'error' => $e->getMessage(),
                'notification_data' => $this->notificationData,
                'user_ids' => $this->userIds
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Notification job failed permanently', [
            'error' => $exception->getMessage(),
            'notification_data' => $this->notificationData,
            'user_ids' => $this->userIds,
            'attempts' => $this->attempts()
        ]);
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'notification',
            'type:' . ($this->notificationData['notification_type'] ?? 'unknown'),
            'priority:' . ($this->notificationData['notification_priority'] ?? 'unknown'),
            'users:' . count($this->userIds)
        ];
    }
}
