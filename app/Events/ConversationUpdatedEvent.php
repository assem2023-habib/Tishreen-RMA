<?php

namespace App\Events;

use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Conversation $conversation;
    public string $action;

    /**
     * Create a new event instance.
     * 
     * @param Conversation $conversation
     * @param string $action - 'assigned', 'closed', 'reopened'
     */
    public function __construct(Conversation $conversation, string $action = 'updated')
    {
        $this->conversation = $conversation;
        $this->action = $action;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('conversation.' . $this->conversation->id),
            new PrivateChannel('user.' . $this->conversation->customer_id),
        ];

        // إرسال للموظف إذا كان معيناً
        if ($this->conversation->employee_id) {
            $channels[] = new PrivateChannel('employee.' . $this->conversation->employee_id);
        }

        // إرسال للقناة العامة للموظفين (للمحادثات الجديدة)
        if ($this->action === 'created' || $this->action === 'assigned') {
            $channels[] = new PrivateChannel('support.queue');
        }

        return $channels;
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'conversation.' . $this->action;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->conversation->id,
            'customer_id' => $this->conversation->customer_id,
            'customer_name' => $this->conversation->customer->full_name ?? $this->conversation->customer->user_name,
            'employee_id' => $this->conversation->employee_id,
            'subject' => $this->conversation->subject,
            'status' => $this->conversation->status,
            'related_type' => $this->conversation->related_type,
            'related_id' => $this->conversation->related_id,
            'last_message_at' => $this->conversation->last_message_at?->toISOString(),
            'action' => $this->action,
            'updated_at' => now()->toISOString(),
        ];
    }
}
