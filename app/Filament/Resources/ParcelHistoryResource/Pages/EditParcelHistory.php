<?php

namespace App\Filament\Resources\ParcelHistoryResource\Pages;

use App\Filament\Resources\ParcelHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditParcelHistory extends EditRecord
{
    protected static string $resource = ParcelHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
