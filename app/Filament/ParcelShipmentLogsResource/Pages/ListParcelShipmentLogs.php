<?php

namespace App\Filament\Resources\ParcelShipmentLogsResource\Pages;

use App\Filament\Resources\ParcelShipmentLogsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParcelShipmentLogs extends ListRecords
{
    protected static string $resource = ParcelShipmentLogsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
