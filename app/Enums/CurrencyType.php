<?php

namespace App\Enums;

enum CurrencyType: string
{
    case SYRIA = 'Syria';
    case USA = 'USA';
    case EUROPE = 'Europe';
    case RUSSIA = 'Russia';
    public function label()
    {
        return match ($this) {
            self::SYRIA => '🇸🇾 سوريا (SYP)',
            self::USA => '🇺🇸 أمريكا (USD)',
            self::EUROPE => '🇪🇺 أوروبا (EUR)',
            self::RUSSIA => '🇷🇺 روسيا (RUB)',
        };
    }
    public function currency()
    {
        return match ($this) {
            self::SYRIA => 'SYP',
            self::USA => 'USD',
            self::EUROPE => 'EUR',
            self::RUSSIA => 'RUB',
        };
    }
    public function currencyIcon()
    {
        return match ($this) {
            self::SYRIA => 'ل.س',    // الليرة السورية
            self::USA => '$',        // الدولار الأمريكي
            self::EUROPE => '€',     // اليورو
            self::RUSSIA => '₽',     // الروبل الروسي
        };
    }
    public static function options()
    {
        return [
            self::SYRIA->value => self::SYRIA->label(),
            self::USA->value => self::USA->label(),
            self::EUROPE->value => self::EUROPE->label(),
            self::RUSSIA->value => self::RUSSIA->label(),
        ];
    }

    public static function values()
    {
        return array_column(self::cases(), 'value');
    }
}
