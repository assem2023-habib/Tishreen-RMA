<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Enums\RoleName;
use App\Enums\RolesName;
use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotification(): ?Notification
    {
        $user = $this->record->user;
        return Notification::make()
            ->success()
            ->title('Employee Add SuccessFully')
            ->body("The role 'Employee' has been assigned to user" . $user->first_name . " " . $user->last_name . ".");
    }
}
