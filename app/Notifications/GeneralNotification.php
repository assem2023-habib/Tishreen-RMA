<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\PrivateChannel;

class GeneralNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $title;
    public $body;
    public $type;
    public $data;
    protected $notifiableId;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $body, $type, $data = [])
    {
        $this->title = $title;
        $this->body = $body;
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $this->notifiableId = $notifiable->id;
        return [\App\Notifications\Channels\PivotDatabaseChannel::class, 'broadcast'];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function broadcastOn()
    {
        return [new PrivateChannel('user.' . $this->notifiableId)];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'type' => $this->type,
            'data' => $this->data,
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => $this->title,
            'message' => $this->body, // Standardized key
            'notification_type' => $this->type, // Standardized key
            'data' => $this->data,
        ]);
    }

    /**
     * Get the broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'notification.sent';
    }
}
