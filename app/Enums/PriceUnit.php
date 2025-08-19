<?php

namespace App\Enums;

enum PriceUnit: string
{
    case KG = "Kg";
    case LBS = "Lbs";
    case KM = "Km";
    case PER_PARCEL = 'PerParcel';
    public function label()
    {
        return match ($this) {
            self::KG => 'كيلو غرام (KG)',
            self::KM => 'كيلو متر (KM)',
            self::PER_PARCEL => 'لكل طرد (PerParcel)',
        };
    }
    
    public static function options()
    {
        return [
            self::KG->value => self::KG->label(),
            self::KM->value => self::KM->label(),
            self::PER_PARCEL->value => self::PER_PARCEL->label(),
        ];
    }
    public static function values()
    {
        return array_column(self::cases(), 'value');
    }
}
