<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRestriction extends Model
{
    protected $fillable = [
        'user_id',
        'restriction_type',
        'reason',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime:H:i',
        'ends_at' => 'datetime:H:i',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
