<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GuestUser;
use App\Models\User;
use App\Notifications\GeneralNotification;

class ParcelAuthorization extends Model
{
    protected static function booted()
    {
        static::updated(function ($auth) {
            if ($auth->isDirty('authorized_status')) {
                $user = $auth->user;
                if ($user) {
                    $statusLabel = $auth->authorized_status; // Assuming status is 'Confirmed', 'Cancelled', etc.
                    $title = $auth->authorized_status === 'Confirmed' ? 'تأكيد التخويل' : 'إلغاء التخويل';
                    $body = $auth->authorized_status === 'Confirmed' 
                        ? "تم تأكيد طلب التخويل الخاص بك للطرد رقم {$auth->parcel->tracking_number}"
                        : "تم إلغاء طلب التخويل الخاص بك للطرد رقم {$auth->parcel->tracking_number}";

                    $user->notify(new GeneralNotification(
                        $title,
                        $body,
                        'authorization_status_updated',
                        ['auth_id' => $auth->id, 'parcel_id' => $auth->parcel_id, 'status' => $auth->authorized_status]
                    ));
                }
            }
        });
    }
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
