<?php

namespace App\Http\Controllers\Api\V1\Chat;

use App\Events\ConversationUpdatedEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\StoreConversationRequest;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    /**
     * قائمة المحادثات الخاصة بالمستخدم الحالي.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $conversations = Conversation::where('customer_id', $user->id)
            ->with(['lastMessage', 'employee'])
            ->orderByDesc('updated_at')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $conversations
        ]);
    }

    /**
     * إنشاء محادثة جديدة.
     */
    public function store(StoreConversationRequest $request)
    {
        $user = $request->user();

        try {
            DB::beginTransaction();

            // تجهيز نوع الكائن المرتبط إذا وجد
            $relatedType = null;
            if ($request->related_type) {
                // تحويل الاسم المختصر إلى اسم الكلاس الكامل
                $map = [
                    'parcel' => 'App\Models\Parcel',
                    'branch' => 'App\Models\Branch',
                    'appointment' => 'App\Models\Appointment',
                ];
                $relatedType = $map[$request->related_type] ?? $request->related_type;
            }

            $conversation = Conversation::create([
                'customer_id' => $user->id,
                'subject' => $request->subject,
                'related_id' => $request->related_id,
                'related_type' => $relatedType,
                'status' => 'pending', // يبدأ في قائمة الانتظار
            ]);

            // بث حدث لإنشاء محادثة جديدة ليراها الموظفون
            broadcast(new ConversationUpdatedEvent($conversation, 'created'))->toOthers();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Conversation started successfully',
                'data' => $conversation
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to start conversation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض تفاصيل المحادثة.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        $conversation = Conversation::where('id', $id)
            ->where('customer_id', $user->id)
            ->with(['employee']) // الرسائل نجلبها عبر endpoint منفصل أو هنا
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $conversation
        ]);
    }

    /**
     * إغلاق المحادثة من قبل العميل (اختياري).
     */
    public function close(Request $request, $id)
    {
        $user = $request->user();
        
        $conversation = Conversation::where('id', $id)
            ->where('customer_id', $user->id)
            ->firstOrFail();

        $conversation->close();
        
        broadcast(new ConversationUpdatedEvent($conversation, 'closed'))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Conversation closed successfully'
        ]);
    }
}
