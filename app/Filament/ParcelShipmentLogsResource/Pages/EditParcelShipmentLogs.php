<?php

namespace App\Filament\Resources\ParcelShipmentLogsResource\Pages;

use App\Filament\Resources\ParcelShipmentLogsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditParcelShipmentLogs extends EditRecord
{
    protected static string $resource = ParcelShipmentLogsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
