<?php

namespace App\Http\Controllers\Api\V1\Notification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Notification\StoreNotificationRequest;
use App\Services\NotificationService;
use App\Trait\ApiResponse;
use Illuminate\Http\Request;

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
        $notifications = $this->notificationService->getUserNotifications($request->user());

        return $this->successResponse(
            $notifications,
            __('notifications.fetched')
        );
    }

    public function store(StoreNotificationRequest $request)
    {
        $notification = $this->notificationService->createNotification(
            $request->validated(),
            $request->input('user_ids', [])
        );

        return $this->successResponse(
            $notification,
            __('notifications.created'),
            201
        );
    }

    public function markAsRead($id, Request $request)
    {
        try {
            $notification = $this->notificationService->markAsRead($request->user(), $id);

            return $this->successResponse(
                $notification,
                __('notifications.marked_as_read')
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                __('notifications.not_found'),
                404
            );
        }
    }
}
