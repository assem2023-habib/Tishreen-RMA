<?php

namespace App\Filament\Resources\UserRestrictionResource\Pages;

use App\Filament\Resources\UserRestrictionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUserRestriction extends CreateRecord
{
    protected static string $resource = UserRestrictionResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
