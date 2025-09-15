<?php

namespace App\Enums;

enum UserAccountStatus: string
{
    case FROZEN = 'Frozen';
    case BANED = 'Banned';
    public static function values()
    {
        return array_column(self::cases(), 'value');
    }
}
