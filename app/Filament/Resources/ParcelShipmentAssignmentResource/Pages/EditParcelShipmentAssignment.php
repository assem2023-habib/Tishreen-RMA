<?php

namespace App\Filament\Resources\ParcelShipmentAssignmentResource\Pages;

use App\Filament\Resources\ParcelShipmentAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditParcelShipmentAssignment extends EditRecord
{
    protected static string $resource = ParcelShipmentAssignmentResource::class;

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
