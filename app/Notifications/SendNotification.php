<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;

class SendNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public array $data;

    protected $notifiable_id;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->queue = 'notifications'; // استخدام queue منفصل للإشعارات
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        $this->notifiable_id = $notifiable->id;
        return [\App\Notifications\Channels\PivotDatabaseChannel::class, 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->data['title'] ?? 'إشعار جديد')
            ->line($this->data['message'] ?? 'لديك إشعار جديد')
            ->action('عرض الإشعار', url('/notifications'))
            ->line('شكراً لاستخدام تطبيقنا!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'id' => $this->id,
            'title' => $this->data['title'] ?? '',
            'message' => $this->data['message'] ?? '',
            'notification_type' => $this->data['notification_type'] ?? 'info',
            'notification_priority' => $this->data['notification_priority'] ?? 'normal',
            'user_id' => $notifiable->id,
            'created_at' => now()->toISOString(),
            'data' => $this->data,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->id,
            'title' => $this->data['title'] ?? '',
            'message' => $this->data['message'] ?? '',
            'notification_type' => $this->data['notification_type'] ?? 'info',
            'notification_priority' => $this->data['notification_priority'] ?? 'normal',
            'user_id' => $notifiable->id,
            'user_name' => $notifiable->name ?? $notifiable->first_name . ' ' . $notifiable->last_name,
            'created_at' => now()->toISOString(),
            'data' => $this->data,
            'type' => 'notification', // لتمييز نوع الرسالة في Flutter
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->notifiable_id),
            new Channel('notifications'),
        ];
    }

    /**
     * Get the broadcast event name.
     */
    public function broadcastAs(): string
    {
        return 'notification.sent';
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return [
            'notification',
            'user:' . ($this->data['user_ids'][0] ?? 'all'),
            'type:' . ($this->data['notification_type'] ?? 'unknown'),
        ];
    }
}
