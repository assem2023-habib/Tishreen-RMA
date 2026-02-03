<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Chat Window (Left/Center) -->
        <div class="lg:col-span-2">
            @livewire('chat-window', ['conversation' => $record])
        </div>

        <!-- Info Sidebar (Right) -->
        <div class="lg:col-span-1 space-y-4">
            <x-filament::section>
                <h3 class="font-bold mb-2">معلومات المحادثة</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">الموضوع:</span>
                        <span>{{ $record->subject }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">الحالة:</span>
                        <span
                            class="{{ $record->status === 'open' ? 'text-green-600' : ($record->status === 'closed' ? 'text-red-600' : 'text-yellow-600') }}">
                            {{ match ($record->status) {'pending' => 'في الانتظار','open' => 'جارية','closed' => 'مغلقة',default => ''} }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">تاريخ الإنشاء:</span>
                        <span>{{ $record->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <h3 class="font-bold mb-2">معلومات العميل</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">الاسم:</span>
                        <span>{{ $record->customer->full_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">الهاتف:</span>
                        <span>{{ $record->customer->phone }}</span>
                    </div>
                </div>
            </x-filament::section>

            @if ($record->related)
                <x-filament::section>
                    <h3 class="font-bold mb-2">مرتبط بـ</h3>
                    <div class="text-sm">
                        @if ($record->related_type === 'App\\Models\\Parcel')
                            <div>📦 طرد: <span class="font-mono">{{ $record->related->tracking_number }}</span></div>
                        @elseif($record->related_type === 'App\\Models\\Branch')
                            <div>🏢 فرع: {{ $record->related->name }}</div>
                        @else
                            {{ class_basename($record->related_type) }} #{{ $record->related_id }}
                        @endif
                    </div>
                </x-filament::section>
            @endif
        </div>
    </div>
</x-filament-panels::page>
