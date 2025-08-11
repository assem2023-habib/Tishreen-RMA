<?php

namespace App\Filament\Widgets;

use App\Models\Parcel;
use App\Models\Rate;
use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';
    protected function getStats(): array
    {
        return [
            Stat::make('number of parcels : ', Parcel::count())
                ->description('the number of parcels in the system : ')
                ->descriptionIcon('heroicon-o-cube', IconPosition::Before)
                ->color('success')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'wire:click' => "\$dispatch('setStatusFilter', {filter : 'processed'})",
                ]),
            Stat::make('rates', Rate::count())
                ->description('the number of rates : ')
                ->descriptionIcon('heroicon-o-star', IconPosition::Before)
                ->color('primary')
                ->chart([1, 2, 3, 4, 5, 6, 7, 8]),
            Stat::make('users', User::count())
                ->description('The number of users : ')
                ->descriptionIcon('heroicon-o-user')
                ->color('secondary')
                ->chart([1, 5, 10, 20, 25]),
        ];
    }
}
