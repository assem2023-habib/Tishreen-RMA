<?php

namespace App\Observers;

use App\Enums\SenderType;
use App\Models\Parcel;
use App\Notifications\GeneralNotification;

class ParcelObserver
{
    /**
     * Handle the Parcel "updated" event.
     */
    public function updated(Parcel $parcel): void
    {
        if ($parcel->isDirty('parcel_status')) {
            $user = null;
            if ($parcel->sender_type === SenderType::AUTHENTICATED_USER) {
                $user = $parcel->sender;
            }

            if ($user) {
                $user->notify(new GeneralNotification(
                    'تحديث حالة الطرد',
                    "تم تغيير حالة طردك رقم {$parcel->tracking_number} إلى {$parcel->parcel_status->value}",
                    'parcel_status_updated',
                    ['parcel_id' => $parcel->id, 'tracking_number' => $parcel->tracking_number]
                ));
            }
        }
    }
}
