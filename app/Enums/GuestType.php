<?php

namespace App\Enums;

enum GuestType: string
{
    //['Sender', 'Authorized']
    case SENDER = 'Sender';
    case AUTHORIZED = 'Authorized';
    public static function values()
    {
        return array_column(self::cases(), 'value');
    }
}
