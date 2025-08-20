<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramOtp extends Model
{
    protected $fillable = ['chat_id', 'otp', 'expires_at'];
    protected $dates = ['expires_at'];
}
