<?php

namespace App\Filament\Resources\GuestUserResource\Pages;

use App\Filament\Resources\GuestUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGuestUsers extends ListRecords
{
    protected static string $resource = GuestUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
