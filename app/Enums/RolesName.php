<?php

namespace App\Enums;

enum RolesName: string
{
    case SUPER_ADMIN = 'Super_Admin';
    case EMPLOYEE = 'Employee';
    public static function values()
    {
        return array_column(self::cases(), 'value');
    }
}
