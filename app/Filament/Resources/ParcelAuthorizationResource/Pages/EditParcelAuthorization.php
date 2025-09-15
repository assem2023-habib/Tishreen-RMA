<?php

namespace App\Filament\Resources\ParcelAuthorizationResource\Pages;

use App\Filament\Resources\ParcelAuthorizationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditParcelAuthorization extends EditRecord
{
    protected static string $resource = ParcelAuthorizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
