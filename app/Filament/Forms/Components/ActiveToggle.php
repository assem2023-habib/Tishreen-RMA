<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Toggle;

class ActiveToggle
{
    /**
     * إنشاء Toggle قابل لإعادة الاستخدام
     *
     * @param string $name اسم الحقل
     * @param string|null $label نص التسمية
     * @return Toggle
     */
    public static function make(string $name = 'is_active', ?string $label = null): Toggle
    {
        return Toggle::make($name)
            ->label($label ?? 'Status')
            ->onIcon('heroicon-o-check-circle')
            ->offIcon('heroicon-o-no-symbol')
            ->onColor('success')
            ->offColor('danger')
            ->extraAttributes(['class' => 'ml-auto']);
    }

    /**
     * إنشاء Toggle مع محاذاة خاصة للـ RTL
     *
     * @param string $name
     * @param string|null $label
     * @return Toggle
     */
    public static function makeRtl(string $name = 'is_active', ?string $label = null): Toggle
    {
        return self::make($name, $label)
            ->extraAttributes(['class' => 'justify-end-safe', 'dir' => 'rtl']);
    }
}
