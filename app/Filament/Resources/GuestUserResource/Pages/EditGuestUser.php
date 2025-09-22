<?php

namespace App\Filament\Resources\GuestUserResource\Pages;

use App\Filament\Resources\GuestUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGuestUser extends EditRecord
{
    protected static string $resource = GuestUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
