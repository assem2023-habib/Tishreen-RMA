<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ParcelResource\Widgets\ParcelChart;
use App\Filament\Widgets\ParcelStatusChart;
use App\Filament\Widgets\RatesChart;
use App\Filament\Widgets\StatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            RatesChart::class,
            ParcelStatusChart::class,
        ];
    }

    public static function canView(): bool
    {
        return true;
    }
}
