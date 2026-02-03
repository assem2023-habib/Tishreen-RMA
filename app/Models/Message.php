<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Message extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'conversation_id',
        'sender_type',
        'sender_id',
        'content',
        'type',
        'attachment_url',
        'attachment_name',
        'read_at',
        'created_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($message) {
            if (empty($message->uuid)) {
                $message->uuid = (string) Str::uuid();
            }
            if (empty($message->created_at)) {
                $message->created_at = now();
            }
        });
    }

    /**
     * المحادثة التي تنتمي لها الرسالة
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * مرسل الرسالة (User أو Employee)
     */
    public function sender(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * هل الرسالة مقروءة؟
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * تحديد الرسالة كمقروءة
     */
    public function markAsRead(): bool
    {
        if ($this->isRead()) {
            return true;
        }

        return $this->update(['read_at' => now()]);
    }

    /**
     * هل المرسل هو العميل؟
     */
    public function isFromCustomer(): bool
    {
        return $this->sender_type === User::class || $this->sender_type === 'App\\Models\\User';
    }

    /**
     * هل المرسل هو الموظف؟
     */
    public function isFromEmployee(): bool
    {
        return $this->sender_type === Employee::class || $this->sender_type === 'App\\Models\\Employee';
    }

    /**
     * الحصول على اسم المرسل
     */
    public function getSenderNameAttribute(): string
    {
        if ($this->sender) {
            if ($this->isFromCustomer()) {
                return $this->sender->full_name ?? $this->sender->user_name ?? 'عميل';
            }
            return $this->sender->user->full_name ?? 'موظف';
        }
        return 'غير معروف';
    }
}
