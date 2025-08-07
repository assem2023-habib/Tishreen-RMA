<?php

namespace App\Filament\Resources\BranchRouteDaysResource\Pages;

use App\Filament\Resources\BranchRouteDaysResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBranchRouteDays extends ListRecords
{
    protected static string $resource = BranchRouteDaysResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
