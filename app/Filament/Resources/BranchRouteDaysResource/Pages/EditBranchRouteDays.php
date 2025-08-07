<?php

namespace App\Filament\Resources\BranchRouteDaysResource\Pages;

use App\Filament\Resources\BranchRouteDaysResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBranchRouteDays extends EditRecord
{
    protected static string $resource = BranchRouteDaysResource::class;

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
