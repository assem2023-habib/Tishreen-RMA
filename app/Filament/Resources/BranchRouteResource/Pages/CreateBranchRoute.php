<?php

namespace App\Filament\Resources\BranchRouteResource\Pages;

use App\Filament\Resources\BranchRouteResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBranchRoute extends CreateRecord
{
    protected static string $resource = BranchRouteResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
