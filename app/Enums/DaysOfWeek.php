<?php

namespace App\Enums;

enum DaysOfWeek: string
{
    case SUNDAY = "Sunday";
    case MONDAY = "Monday";
    case TUESDAY = "Tuesday";
    case WEDNESDAY = "Wednesday";
    case THURSDAY = "Thursday";
    case FRIDAY = "FRIDAY";
    case SATURDAY = "Saturday";

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }
}
