<?php

namespace App\Enums;

enum PermissionName: string
{
    case CAN_ACCESS_PANEL = "can_access_panel";
    public static function values()
    {
        return array_column(self::cases(), 'value');
    }
}
