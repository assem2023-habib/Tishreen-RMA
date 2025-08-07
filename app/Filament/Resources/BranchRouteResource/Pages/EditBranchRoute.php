<?php

namespace App\Filament\Resources\BranchRouteResource\Pages;

use App\Filament\Resources\BranchRouteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBranchRoute extends EditRecord
{
    protected static string $resource = BranchRouteResource::class;

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
