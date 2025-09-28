<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use App\Models\Country;

class LocationSelect
{
    public static function make($cityId = 'city_id', $countryId = 'country_id', $cityLabel = 'City', $countryLabel = 'Country'): Grid
    {
        return Grid::make(2)
            ->schema([
                Select::make($countryId)
                    ->label($countryLabel)
                    ->options(Country::all()->pluck('en_name', 'id'))
                    ->default(Country::query()->value('id'))
                    ->live()
                    ->afterStateUpdated(fn(callable $set) => $set($cityId, null))
                    ->validationMessages([
                        'required' => 'Please select a country',
                    ]),
                Select::make($cityId)
                    ->label($cityLabel)
                    ->options(function (callable $get) use ($countryId) {
                        $country = Country::find($get($countryId));
                        return $country ? $country->cities()->pluck('en_name', 'id') : [];
                    })
                    ->validationMessages([
                        'required' => 'Please select a city',
                    ]),
            ]);
    }
}
