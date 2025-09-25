<?php

namespace App\Filament\Resources\ParcelShipmentAssignmentResource\Pages;

use App\Filament\Resources\ParcelShipmentAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParcelShipmentAssignments extends ListRecords
{
    protected static string $resource = ParcelShipmentAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
