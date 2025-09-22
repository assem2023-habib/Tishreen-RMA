<?php

namespace App\Filament\Resources\GuestUserResource\Pages;

use App\Filament\Resources\GuestUserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGuestUser extends CreateRecord
{
    protected static string $resource = GuestUserResource::class;
}
