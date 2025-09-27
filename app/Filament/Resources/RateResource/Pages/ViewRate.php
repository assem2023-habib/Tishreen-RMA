<?php

namespace App\Filament\Resources\RateResource\Pages;

use App\Enums\RatingForType;
use App\Filament\Resources\RateResource;
use App\Models\{Branch, Employee};
use Filament\Infolists\Components\{Section, TextEntry, Grid};
use Filament\Resources\Pages\ViewRecord;

class ViewRate extends ViewRecord
{
    protected static string $resource = RateResource::class;

    public function infolist(\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        return $infolist
            ->schema([
                Section::make('Rating Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('rating')
                                    ->label('Rating')
                                    ->formatStateUsing(
                                        fn($state) =>
                                        str_repeat('⭐', (int) $state)
                                    )
                                    ->color('warning')
                                    ->size('lg'),

                                TextEntry::make('comment')
                                    ->label('Comment')
                                    ->placeholder('No comment')
                                    ->size('md')
                                    ->columnSpanFull(),

                                TextEntry::make('user.name')
                                    ->label('Rated By')
                                    ->badge()
                                    ->color('success'),

                                TextEntry::make('rateable_type')
                                    ->label('Type')
                                    ->formatStateUsing(fn($state) => ucfirst($state))
                                    ->badge()
                                    ->color(fn($state) => RatingForType::color($state)),
                                TextEntry::make('rateable_name')
                                    ->label('Related Details')
                                    ->color('primary')
                                    ->weight('bold'),

                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime('Y-m-d H:i')
                                    ->color('gray'),
                            ])
                    ]),
            ]);
    }
}
