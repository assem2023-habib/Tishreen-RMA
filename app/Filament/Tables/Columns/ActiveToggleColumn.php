<?php

namespace App\Filament\Tables\Columns;

use Filament\Tables\Columns\ToggleColumn;


class ActiveToggleColumn
{
    /**
     * إنشاء ToggleColumn قابل لإعادة الاستخدام
     *
     * @param string $name اسم الحقل
     * @return ToggleColumn
     */
    public static function make(string $name = 'is_active'): ToggleColumn
    {
        return ToggleColumn::make($name)
            ->onIcon('heroicon-o-check-circle')
            ->offIcon('heroicon-o-no-symbol')
            ->onColor('success')
            ->offColor('danger')
            ->sortable();
    }
}
