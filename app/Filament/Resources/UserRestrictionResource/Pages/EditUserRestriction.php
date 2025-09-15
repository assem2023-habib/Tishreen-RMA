<?php

namespace App\Filament\Resources\UserRestrictionResource\Pages;

use App\Filament\Resources\UserRestrictionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserRestriction extends EditRecord
{
    protected static string $resource = UserRestrictionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
