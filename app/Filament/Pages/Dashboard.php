<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RatesChart;
use App\Filament\Widgets\StatsOverview;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';
    public function getWidgets()
    {
        return [
            StatsOverview::class,
            RatesChart::class,
        ];
    }
    public static function canView(): bool
    {
        return true;
    }
}
