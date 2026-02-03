<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Conversation extends Model
{
    protected $fillable = [
        'customer_id',
        'employee_id',
        'related_type',
        'related_id',
        'subject',
        'status',
        'last_message_at',
        'taken_at',
        'closed_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'taken_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /**
     * العميل صاحب المحادثة
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * الموظف المسؤول عن المحادثة
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * الكائن المرتبط (طرد، فرع، إلخ) - علاقة polymorphic
     */
    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * رسائل المحادثة
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * آخر رسالة في المحادثة
     */
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    /**
     * المحادثات المعلقة (في انتظار موظف)
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * المحادثات المفتوحة
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * المحادثات المغلقة
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * المحادثات التي لم يأخذها موظف بعد
     */
    public function scopeUnassigned($query)
    {
        return $query->whereNull('employee_id')->where('status', 'pending');
    }

    /**
     * تعيين موظف للمحادثة
     */
    public function assignToEmployee(Employee $employee): bool
    {
        return $this->update([
            'employee_id' => $employee->id,
            'status' => 'open',
            'taken_at' => now(),
        ]);
    }

    /**
     * إغلاق المحادثة
     */
    public function close(): bool
    {
        return $this->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);
    }

    /**
     * الرسائل غير المقروءة
     */
    public function unreadMessages()
    {
        return $this->messages()->whereNull('read_at');
    }

    /**
     * عدد الرسائل غير المقروءة
     */
    public function getUnreadCountAttribute(): int
    {
        return $this->unreadMessages()->count();
    }
}
