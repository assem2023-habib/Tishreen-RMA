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
        // $base = Str::before($data['email'], '@');
        // $username = $base;
        // $count = 1;

        // while (User::where('user_name', $username)->exists()) {
        //     $username = $base . $count++;
        // }

        // $data['user_name'] = $username;

        // return $data;
        // if ($data['account_status'] !== UserAccountStatus::BANED->value || $data['account_status'] !== UserAccountStatus::FROZEN->value) {
        //     UserRestriction::create([
        //         'user_id' => $data['id'],
        //         'type' => $data['account_status'],
        //         'reason' => '',
        //         'starts_at' => now(),
        //         'ends_at' => '',
        //         'is_active' => 1,

        //     ]);
        // }
        if (!isEmpty($data['is_verified'])) {
            $data['email_verified_at'] = Carbon::now();
        } else {
            $data['email_verified_at'] = null;
        }
        unset($data['is_verified'], $data['account_status']);
        return $data;
    }
}
