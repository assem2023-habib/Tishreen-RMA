<?php

namespace App\Filament\Resources\BranchRouteResource\Pages;

use App\Filament\Resources\BranchRouteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBranchRoutes extends ListRecords
{
    protected static string $resource = BranchRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
