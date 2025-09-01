<?php

namespace App\Console\Commands;

use App\Jobs\ProcessNotificationJob;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:test {--user-id= : Test with specific user ID} {--type=info : Notification type} {--priority=Reminder : Notification priority}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the notification system';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService)
    {
        $this->info('🧪 Testing notification system...');

        try {
            $userId = $this->option('user-id');
            $type = $this->option('type');
            $priority = $this->option('priority');

            // إذا لم يتم تحديد user_id، اختر أول مستخدم
            if (!$userId) {
                $user = User::first();
                if (!$user) {
                    $this->error('❌ No users found in the system');
                    return 1;
                }
                $userId = $user->id;
            } else {
                $user = User::find($userId);
                if (!$user) {
                    $this->error("❌ User with ID {$userId} not found");
                    return 1;
                }
            }

            $this->info("👤 Testing with user: {$user->first_name} {$user->last_name} (ID: {$user->id})");

            // إنشاء بيانات الإشعار التجريبي
            $notificationData = [
                'title' => 'إشعار تجريبي من Command',
                'message' => 'هذا إشعار تجريبي لاختبار النظام',
                'notification_type' => $type,
                'notification_priority' => $priority,
            ];

            $this->info("📝 Notification data: " . json_encode($notificationData, JSON_UNESCAPED_UNICODE));

            // اختبار 1: إنشاء إشعار مباشر
            $this->info("\n🔔 Test 1: Direct notification creation...");
            $notification = $notificationService->createNotification($notificationData, [$userId]);
            $this->info("✅ Notification created with ID: {$notification->id}");

            // اختبار 2: إرسال إشعار
            $this->info("\n📤 Test 2: Sending notification...");
            $result = $notificationService->sendNotification($notificationData, [$userId]);
            $this->info("✅ Notification sent successfully");

            // اختبار 3: إرسال Job
            $this->info("\n⚡ Test 3: Dispatching notification job...");
            ProcessNotificationJob::dispatch($notificationData, [$userId]);
            $this->info("✅ Job dispatched to queue");

            // اختبار 4: الحصول على إحصائيات
            $this->info("\n📊 Test 4: Getting notification stats...");
            $stats = $notificationService->getNotificationStats($user);
            $this->info("✅ Stats retrieved: " . json_encode($stats));

            // اختبار 5: اختبار البث المباشر
            $this->info("\n📡 Test 5: Testing broadcast...");
            try {
                $broadcastResult = $notificationService->sendNotification($notificationData, [$userId]);
                $this->info("✅ Broadcast test completed");
            } catch (\Exception $e) {
                $this->warn("⚠️ Broadcast test failed: {$e->getMessage()}");
            }

            $this->info("\n🎉 All tests completed successfully!");
            $this->info("📋 Summary:");
            $this->info("   - User: {$user->first_name} {$user->last_name}");
            $this->info("   - Notification ID: {$notification->id}");
            $this->info("   - Type: {$type}");
            $this->info("   - Priority: {$priority}");
            $this->info("   - Total notifications: {$stats['total']}");
            $this->info("   - Unread: {$stats['unread']}");

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Test failed: {$e->getMessage()}");
            Log::error('Notification test command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
