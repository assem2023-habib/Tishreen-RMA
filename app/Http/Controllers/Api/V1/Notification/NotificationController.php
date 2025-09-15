<?php

namespace App\Http\Controllers\Api\V1\Notification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Notification\StoreNotificationRequest;
use App\Services\NotificationService;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    use ApiResponse;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 50);
        $unreadOnly = $request->boolean('unread_only', false);

        $notifications = $this->notificationService->getUserNotifications($user, $limit, $unreadOnly);

        return $this->successResponse([
            'notifications' => $notifications,
            'stats' => $this->notificationService->getNotificationStats($user)
        ], __('notifications.fetched'));
    }

    public function store(StoreNotificationRequest $request)
    {
        $data = $request->validated();
        $notification = $this->notificationService->sendNotification($data, $data['user_ids'] ?? null);

        return $this->successResponse([
            'notification' => $notification
        ], __('notifications.created'), 201);
    }

    public function markAsRead($id)
    {
        $user = Auth::user();

        try {
            $notification = $this->notificationService->markAsRead($user, $id);

            return $this->successResponse([
                'notification' => $notification
            ], __('notifications.marked_as_read'));
        } catch (\Exception $e) {
            return $this->errorResponse(__('notifications.not_found'), 404);
        }
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $count = $this->notificationService->markAllAsRead($user);

        return $this->successResponse([
            'marked_count' => $count
        ], __('notifications.all_marked_as_read'));
    }

    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = $this->notificationService->getUnreadCount($user);

        return $this->successResponse([
            'unread_count' => $count
        ], 'Unread count retrieved');
    }

    public function getStats()
    {
        $user = Auth::user();
        $stats = $this->notificationService->getNotificationStats($user);

        return $this->successResponse([
            'stats' => $stats
        ], 'Notification stats retrieved');
    }

    public function delete($id)
    {
        $user = Auth::user();

        try {
            $this->notificationService->deleteNotification($id, $user);

            return $this->successResponse([], __('notifications.deleted'));
        } catch (\Exception $e) {
            return $this->errorResponse(__('notifications.not_found'), 404);
        }
    }

    public function sendBulk(Request $request)
    {
        $request->validate([
            'notifications' => 'required|array|min:1',
            'notifications.*.title' => 'required|string|max:255',
            'notifications.*.message' => 'required|string|max:512',
            'notifications.*.notification_type' => 'required|string',
            'notifications.*.notification_priority' => 'required|string',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id'
        ]);

        $results = $this->notificationService->sendBulkNotifications(
            $request->notifications,
            $request->user_ids
        );

        return $this->successResponse([
            'results' => $results
        ], 'Bulk notifications sent');
    }

    public function testBroadcast()
    {
        $user = Auth::user();

        // إرسال إشعار تجريبي
        $testData = [
            'title' => 'إشعار تجريبي',
            'message' => 'هذا إشعار تجريبي لاختبار البث المباشر',
            'notification_type' => 'info',
            'notification_priority' => 'Reminder',
            'user_ids' => [$user->id]
        ];

        $notification = $this->notificationService->sendNotification($testData, [$user->id]);

        return $this->successResponse([
            'message' => 'Test notification sent',
            'notification' => $notification
        ], 'Test notification sent successfully');
    }
}
