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
    public function users()
    {
        return $this->belongsToMany(User::class, 'notification_user', 'notification_id', 'notifiable_id')
            ->wherePivot('notifiable_type', User::class)
            ->withPivot(['data', 'read_at'])
            ->withTimestamps();
    }
}
