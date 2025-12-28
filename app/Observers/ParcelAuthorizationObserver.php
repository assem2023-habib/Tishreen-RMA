<?php

namespace App\Observers;

use App\Models\ParcelAuthorization;
use App\Notifications\GeneralNotification;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ParcelAuthorizationObserver
{
    /**
     * Handle the ParcelAuthorization "created" event.
     */
    public function creating(ParcelAuthorization $parcelAuthorization): void
    {
        do {
            $authorized_code = strtoupper(Str::random(10));
        } while (ParcelAuthorization::where('authorized_code', $authorized_code)->exists());
        $parcelAuthorization->authorized_code = $authorized_code;

        $parcelAuthorization->expired_at = Carbon::parse($parcelAuthorization->generated_at)->addDays(7);
    }
    /**
     * Handle the ParcelAuthorization "updated" event.
     */
    public function updated(ParcelAuthorization $auth): void
    {
        if ($auth->isDirty('authorized_status')) {
            $user = $auth->user;
            if ($user) {
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
    }
}
