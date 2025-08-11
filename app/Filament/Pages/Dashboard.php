<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RatesChart;
use Filament\Pages\Page;
use Filament\Widgets\StatsOverviewWidget;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';
    public function getWidgets()
    {
        return [
            StatsOverviewWidget::class,
            RatesChart::class,
        ];
    }
    public static function canView(): bool
    {
        // return auth()->user();
        return true;
    }
}
