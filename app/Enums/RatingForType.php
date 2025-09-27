<?php

namespace App\Enums;

enum RatingForType: string
{
    case SERVICE = "Service";
    case BRANCH = "Branch";
    case EMPLOYEE = "Employee";
    case PARCEL = "Parcel";
    case DELIVERY = "Delivery";
    case APPLICATION = "Application";
    case CHATSESSION = "ChatSession";
    public static function values()
    {
        return array_column(self::cases(), 'value');
    }

    public static function color(string $state): string
    {
        return match ($state) {
            self::APPLICATION->value => 'success',
            self::SERVICE->value     => 'gray',
            self::BRANCH->value      => 'danger',
            self::EMPLOYEE->value    => 'warning',
            self::PARCEL->value      => 'info',
            self::DELIVERY->value    => 'primary',
            self::CHATSESSION->value => 'secondary',
            default => 'secondary',
        };
    }

    public static function label(string $state): string
    {
        return ucfirst($state);
    }
}
