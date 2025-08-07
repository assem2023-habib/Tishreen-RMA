<?php

namespace App\Filament\Resources\UsagePoliciesResource\Pages;

use App\Filament\Resources\UsagePoliciesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsagePolicies extends ListRecords
{
    protected static string $resource = UsagePoliciesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
