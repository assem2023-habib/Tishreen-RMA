<?php

namespace App\Enums;

enum RoleName: string
{
    case SUPER_ADMIN = "SuperAdmin";
    case EMPLOYEE = "Employee";
    public static function values()
    {
        return array_column(self::cases(), 'value');
    }
}
