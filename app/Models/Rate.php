<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $fillable = [
        'user_id',
        'rateable_id',
        'rating',
        'comment',
        'rateable_type',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function rateable()
    {
        return $this->morphTo();
    }
}
