<?php

namespace App\Filament\Forms\Components;

use App\Models\Country;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;

class City
{
    protected $country;

    public static function make(string $cityName = 'city_id', ?string $label = null)
    {
        return Grid::make(2)->schema([
            Select::make('')
                ->label('Country')
                ->options(Country::select('id', 'en_name,')
                    ->get()
                    ->mapWithKeys(
                        function ($country) {
                            return [$country->id => 'name : ' . $country->en_name];
                        }
                    )),
            Select::make($cityName)
                ->label($label ?? 'City'),
        ]);
    }
}
