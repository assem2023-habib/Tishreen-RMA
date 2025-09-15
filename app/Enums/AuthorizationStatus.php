<?php

namespace App\Enums;

enum AuthorizationStatus: string
{
    case PENDING = 'Pending';
    case ACTIVE = 'Active';
    case EXPIRED = 'Expired';
    case USED = 'Used';
    case CANCELLED = 'Cancelled';
    public static function values(){
        return array_column(self::cases(), 'value');
    }
}
