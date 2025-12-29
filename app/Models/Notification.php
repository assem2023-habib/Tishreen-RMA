<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'message',
        'notification_type',
        'notification_priority',
    ];

    public function scopeUnread($query)
    {
        return $query->whereNull('notification_user.read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('notification_user.read_at');
    }

    public function markAsRead()
    {
        if ($this->pivot) {
            $this->pivot->update(['read_at' => now()]);
        }
    }

    public function getDataAttribute()
    {
        if ($this->pivot && isset($this->pivot->data)) {
            $data = $this->pivot->data;
            return is_string($data) ? json_decode($data, true) : $data;
        }
        return [];
    }

    public function getReadAtAttribute()
    {
        return $this->pivot ? $this->pivot->read_at : null;
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'notification_user', 'notification_id', 'notifiable_id')
            ->wherePivot('notifiable_type', User::class)
            ->withPivot(['data', 'read_at'])
            ->withTimestamps();
    }
}
