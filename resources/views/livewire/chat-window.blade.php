<div class="flex flex-col h-[600px] border rounded-lg bg-white shadow-sm"
    x-data="{ 
        scrollToBottom() { 
            const container = document.getElementById('messages-container');
            if(container) container.scrollTop = container.scrollHeight;
        } 
    }"
    x-init="scrollToBottom()"
    @scroll-to-bottom.window="scrollToBottom()"
>
    <!-- Header -->
    <div class="p-4 border-b bg-gray-50 flex justify-between items-center rounded-t-lg">
        <div>
            <h3 class="font-bold text-lg">{{ $conversation->subject ?? 'محادثة' }}</h3>
            <span class="text-sm text-gray-500">العميل: {{ $conversation->customer->user_name }}</span>
        </div>
        <div>
           <span @class([
               'px-2 py-1 rounded text-xs font-semibold',
               'bg-yellow-100 text-yellow-800' => $conversation->status === 'pending',
               'bg-green-100 text-green-800' => $conversation->status === 'open',
               'bg-red-100 text-red-800' => $conversation->status === 'closed',
           ])>
               {{ match($conversation->status) { 'pending' => 'انتظار', 'open' => 'جارية', 'closed' => 'مغلقة', default => '' } }}
           </span>
        </div>
    </div>

    <!-- Messages Area -->
    <div id="messages-container" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
        @foreach($messages as $msg)
            @php
                // Check if sender is employee or customer based on sender_type
                // If sender_type contains 'Employee', it's outgoing (right)
                // Else (User), it's incoming (left)
                $isEmployee = str_contains($msg['sender_type'], 'Employee');
                $isMe = $isEmployee && ($msg['sender']['user_id'] ?? null) == auth()->id();
                
                // Fallback for current session user just sent
                if(!isset($msg['sender_type']) && isset($msg['uuid'])) {
                     $isEmployee = true; 
                }
            @endphp
            
            <div @class([
                'flex w-full',
                'justify-end' => $isEmployee,
                'justify-start' => !$isEmployee,
            ])>
                <div @class([
                    'max-w-[70%] rounded-lg p-3 shadow-sm',
                    'bg-primary-600 text-white' => $isEmployee,
                    'bg-white text-gray-800 border' => !$isEmployee,
                ])>
                    @if($msg['type'] === 'file' || $msg['type'] === 'image')
                        <div class="mb-1">
                            <a href="{{ $msg['attachment_url'] }}" target="_blank" class="flex items-center gap-2 underline text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                {{ $msg['attachment_name'] ?? 'مرفق' }}
                            </a>
                        </div>
                    @endif
                    
                    @if($msg['content'])
                        <p class="text-sm whitespace-pre-wrap">{{ $msg['content'] }}</p>
                    @endif
                    
                    <div @class([
                        'text-[10px] mt-1 text-right',
                        'text-primary-100' => $isEmployee,
                        'text-gray-400' => !$isEmployee,
                    ])>
                        {{ \Carbon\Carbon::parse($msg['created_at'])->format('H:i') }}
                        @if(!$isEmployee) - {{ $msg['sender_name'] ?? 'العميل' }} @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Input Area -->
    <div class="p-4 bg-white border-t rounded-b-lg">
        @if($conversation->status !== 'closed')
            <form wire:submit.prevent="sendMessage" class="flex gap-2">
                <input type="file" wire:model="attachment" class="hidden" id="file-upload">
                <button type="button" onclick="document.getElementById('file-upload').click()" class="p-2 text-gray-500 hover:text-primary-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                </button>
                
                <input 
                    type="text" 
                    wire:model="newMessage" 
                    placeholder="اكتب رسالتك هنا..." 
                    class="flex-1 rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500"
                >
                
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50" wire:loading.attr="disabled">
                    إرسال
                </button>
            </form>
            @if ($attachment) 
                <div class="text-xs text-green-600 mt-1">تم اختيار ملف: {{ $attachment->getClientOriginalName() }}</div>
            @endif
        @else
            <div class="text-center text-gray-500 py-2">
                هذه المحادثة مغلقة.
            </div>
        @endif
    </div>
</div>
