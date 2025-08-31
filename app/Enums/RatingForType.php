<?php

namespace App\Enums;

enum RatingForType: string
{
    case SERVICE = "Service";
    case BRANCH = "Branch";
    case EMPLOYEE = "Employee";
    case PARCEL = "Parcel";
    case DELIVERY = "Delivery";
    case APPLICATION = "Application";
    case CHATSESSION = "ChatSession";
    public static function values()
    {
        return array_column(self::cases(), 'value');
    }
}
