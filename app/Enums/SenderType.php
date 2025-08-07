<?php

namespace App\Enums;

enum SenderType: string
{
    case AUTHENTICATED_USER = 'Authenticated_user';
    case GUEST_USER = 'Guest_User';

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }
}
