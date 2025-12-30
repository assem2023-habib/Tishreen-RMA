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
        if ($parcel->wasChanged('parcel_status')) {
            $user = $parcel->sender;

            if ($user && ($parcel->sender_type === SenderType::AUTHENTICATED_USER || $parcel->sender_type->value === SenderType::AUTHENTICATED_USER->value)) {
                $statusMessage = $this->getStatusMessage($parcel);

                $user->notify(new GeneralNotification(
                    'تحديث حالة الطرد',
                    $statusMessage,
                    'parcel_status_updated',
                    [
                        'parcel_id' => $parcel->id,
                        'tracking_number' => $parcel->tracking_number,
                        'new_status' => $parcel->parcel_status
                    ]
                ));
            }
        }
    }

    private function getStatusMessage(Parcel $parcel): string
    {
        $status = $parcel->parcel_status;
        $statusValue = $status instanceof \UnitEnum ? $status->value : $status;
        $trackingNumber = $parcel->tracking_number;

        return match ($statusValue) {
            'Pending' => "طردك رقم ($trackingNumber) قيد الانتظار.",
            'Confirmed' => "تم استلام طردك رقم ($trackingNumber) في الفرع بنجاح وهو الآن قيد المعالجة.",
            'Ready_For_Shipping' => "طردك رقم ($trackingNumber) جاهز للشحن الآن وتم ربطه بشحنة.",
            'In_transit' => "طردك رقم ($trackingNumber) في الطريق الآن إلى وجهته.",
            'Out_For_Delivery' => "طردك رقم ($trackingNumber) مع المندوب الآن وفي طريقه إليك.",
            'Ready_For_Pickup' => "طردك رقم ($trackingNumber) وصل إلى فرع الوجهة وهو جاهز للاستلام الآن.",
            'Delivered' => "تم تسليم طردك رقم ($trackingNumber) بنجاح. شكراً لاستخدامكم خدماتنا.",
            'Failed' => "تعذر تسليم طردك رقم ($trackingNumber). يرجى التواصل مع خدمة العملاء.",
            'Returned' => "تم إرجاع طردك رقم ($trackingNumber) إلى المرسل.",
            'Canceled' => "تم إلغاء طردك رقم ($trackingNumber).",
            default => "تم تغيير حالة طردك رقم ($trackingNumber) إلى: {$statusValue}",
        };
    }
}
