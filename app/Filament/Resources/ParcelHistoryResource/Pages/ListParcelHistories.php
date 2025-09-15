<?php

namespace App\Filament\Resources\ParcelHistoryResource\Pages;

use App\Filament\Resources\ParcelHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParcelHistories extends ListRecords
{
    protected static string $resource = ParcelHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
