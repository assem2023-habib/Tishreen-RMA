<?php

namespace App\Enums;

enum PolicyTypes: string
{
    case WEIGHT = 'Weight'; // حسب الوزن
    case DISTANCE = 'Distance'; // حسب المسافة
    case VOLUME = 'Volume'; // حسب الحجم (حسب الابعاد مثلا الطول والعرض والارتفاع)
    case FLAT = 'Flate'; // ثابت
    public static function values()
    {
        return array_column(self::cases(), 'value');
    }
}
