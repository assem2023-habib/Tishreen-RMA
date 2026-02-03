<?php

namespace App\Http\Controllers\Api\V1\Chat;

use App\Events\NewMessageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\StoreMessageRequest;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    /**
     * جلب رسائل محادثة معينة.
     */
    public function index(Request $request, $conversationId)
    {
        $user = $request->user();
        
        // التحقق من صلاحية الوصول للمحادثة
        $conversation = Conversation::where('id', $conversationId)
            ->where(function ($query) use ($user) {
                $query->where('customer_id', $user->id)
                      // يمكن إضافة منطق الموظف هنا إذا استخدمنا نفس الـ endpoint
                      ; 
            })
            ->firstOrFail();
            
        // تحديد الرسائل كمقرؤة عند جلبها
        // ملاحظة: قد نفضل فعل ذلك عبر endpoint منفصل 'mark-read' لتجنب الآثار الجانبية لـ GET
        // لكن للتبسيط سنقوم بذلك هنا للرسائل القادمة من الموظف
        $conversation->messages()
            ->where('sender_type', '!=', get_class($user))
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = $conversation->messages()
            ->with('sender') // لجلب اسم المرسل
            ->orderBy('created_at', 'desc') // الأحدث أولاً للواجهة (chat UI)
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $messages->items(), // نعيد مصفوفة الرسائل
            'meta' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'total' => $messages->total(),
            ]
        ]);
    }

    /**
     * إرسال رسالة جديدة.
     */
    public function store(StoreMessageRequest $request, $conversationId)
    {
        $user = $request->user();
        
        $conversation = Conversation::where('id', $conversationId)
            ->where('customer_id', $user->id)
            ->firstOrFail();

        // لا يمكن إرسال رسالة في محادثة مغلقة
        if ($conversation->status === 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'Conversation is closed'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $attachmentUrl = $request->attachment_url;
            $attachmentName = null;

            // معالجة رفع الملف إذا وجد
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store('chat-attachments', 'public');
                $attachmentUrl = asset('storage/' . $path);
                $attachmentName = $file->getClientOriginalName();
            }

            $message = $conversation->messages()->create([
                'sender_type' => get_class($user),
                'sender_id' => $user->id,
                'content' => $request->content ?? '', // يمكن أن يكون فارغاً إذا كان هناك ملف
                'type' => $request->type ?? ($request->hasFile('attachment') ? 'file' : 'text'),
                'attachment_url' => $attachmentUrl,
                'attachment_name' => $attachmentName,
                'uuid' => (string) Str::uuid(),
            ]);

            // تحديث وقت آخر رسالة في المحادثة
            $conversation->update(['last_message_at' => now()]);

            // بث الحدث
            broadcast(new NewMessageEvent($message))->toOthers();

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $message
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
