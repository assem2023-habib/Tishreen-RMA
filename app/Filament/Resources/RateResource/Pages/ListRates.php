<?php

namespace App\Filament\Resources\RateResource\Pages;

use App\Filament\Resources\RateResource;
use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ListRecords;

class ListRates extends ListRecords
{
    protected static string $resource = RateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('rating')
                    ->label('Rating'),

                TextEntry::make('comment')
                    ->label('Comment'),

                TextEntry::make('user.user_name')
                    ->label('Rated By'),

                TextEntry::make('rateable_type')
                    ->label('Type'),

                TextEntry::make('relatedDetails')
                    ->label('Related Details'),

                TextEntry::make('created_at')
                    ->label('Created At'),
            ]);
    }
}
