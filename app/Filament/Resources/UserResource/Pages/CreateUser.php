<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\UserAccountStatus;
use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Models\UserRestriction;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use League\Csv\Serializer\CastToArray;

use function PHPUnit\Framework\isEmpty;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('User registered')
            ->body('The user has been created successfully.');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!isEmpty($data['is_verified'])) {
            $data['email_verified_at'] = Carbon::now();
        }
        unset($data['is_verified']);
        return $data;
    }
}
