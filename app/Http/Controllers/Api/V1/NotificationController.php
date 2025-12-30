<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // استخدام العلاقة المخصصة التي تعتمد على الجدول الوسيط
        $notifications = $user->notifications()->latest()->paginate(20);

        // تحويل البيانات لتناسب الواجهة الأمامية
        $notifications->getCollection()->transform(function ($notification) {
            // البيانات الآن موجودة في الـ pivot
            $pivotData = $notification->pivot->data;

            // تأكد من أن البيانات ليست سلسلة نصية (JSON)
            if (is_string($pivotData)) {
                $pivotData = json_decode($pivotData, true);
            }

            return [
                'id' => $notification->id,
                'title' => $notification->title ?? ($pivotData['title'] ?? 'No Title'),
                'message' => $notification->message ?? ($pivotData['body'] ?? ($pivotData['message'] ?? '')),
                'type' => $notification->notification_type ?? ($pivotData['type'] ?? 'general'),
                'data' => $pivotData['data'] ?? $pivotData ?? [],
                'is_read' => !is_null($notification->pivot->read_at),
                'read_at' => $notification->pivot->read_at,
                'created_at' => $notification->pivot->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $notifications->items(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ]
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        // البحث في الجدول الوسيط وتحديث read_at
        $user = $request->user();
        $notification = $user->notifications()->findOrFail($id);

        $user->notifications()->updateExistingPivot($id, [
            'read_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
    {
        $user = $request->user();

        $user->notifications()
            ->wherePivot('read_at', null)
            ->updateExistingPivot($user->notifications()->wherePivot('read_at', null)->pluck('notifications.id'), [
                'read_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        // حذف الارتباط من الجدول الوسيط فقط (لا نحذف الرسالة الأصلية لأنها قد تكون لمستخدمين آخرين)
        $user->notifications()->detach($id);

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted from your list'
        ]);
    }
}
