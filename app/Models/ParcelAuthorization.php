<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GuestUser;
use App\Models\User;
use App\Notifications\GeneralNotification;

class ParcelAuthorization extends Model
{
    protected $fillable = [
        'user_id',
        'parcel_id',
        'authorized_user_id',
        'authorized_user_type',
        'authorized_code',
        'authorized_status',
        'generated_at',
        'expired_at',
        'used_at',
        'cancellation_reason',
    ];
    public $timestamps = false;
    public function sender()
    {
        return $this->morphTo();
    }
    public function authorizedUser()
    {
        return $this->morphTo();
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function parcel()
    {
        return $this->belongsTo(Parcel::class);
    }
}
