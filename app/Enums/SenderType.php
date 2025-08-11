<?php

namespace App\Enums;

enum SenderType: string
{
    case AUTHENTICATED_USER = 'User';
    case GUEST_USER = 'GuestUser';

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }
}
