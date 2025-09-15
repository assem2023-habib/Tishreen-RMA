<?php

namespace App\Filament\Resources\UserRestrictionResource\Pages;

use App\Filament\Resources\UserRestrictionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserRestrictions extends ListRecords
{
    protected static string $resource = UserRestrictionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Restriction'),
        ];
    }
}
