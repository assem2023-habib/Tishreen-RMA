<?php

namespace App\Enums;

enum ParcelStatus: string
{
    //Pending, Confirmed, In_transit, Out_For_Delivery, Ready_For_Pickup, Delivered, Failed, Returned, Canceled
    case PENDING = 'Pending';
    case CONFIRMED = 'Confirmed';
    case IN_TRANSIT = 'In_transit';
    case OUT_FOR_DELIVERY = 'Out_For_Delivery';
    case READY_FOR_PICKUP = 'Ready_For_Pickup';
    case DELIVERED = 'Delivered';
    case FAILED = 'Failed';
    case RETURNED = 'Returned';
    case CANCELED = 'Canceled';
    public static function values()
    {
        return array_column(self::cases(), 'value');
    }
}
