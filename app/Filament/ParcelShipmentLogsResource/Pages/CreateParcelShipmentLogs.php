<?php

namespace App\Filament\Resources\ParcelShipmentLogsResource\Pages;

use App\Filament\Resources\ParcelShipmentLogsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateParcelShipmentLogs extends CreateRecord
{
    protected static string $resource = ParcelShipmentLogsResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {

        unset($data['route_id']);
        return $data;
    }
}
