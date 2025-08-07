<?php

namespace App\Filament\Resources\UsagePoliciesResource\Pages;

use App\Filament\Resources\UsagePoliciesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUsagePolicies extends EditRecord
{
    protected static string $resource = UsagePoliciesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
