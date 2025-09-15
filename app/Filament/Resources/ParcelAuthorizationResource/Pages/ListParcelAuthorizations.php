<?php

namespace App\Filament\Resources\ParcelAuthorizationResource\Pages;

use App\Filament\Resources\ParcelAuthorizationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParcelAuthorizations extends ListRecords
{
    protected static string $resource = ParcelAuthorizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
