<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\UserAccountStatus;
use App\Filament\Resources\UserResource;
use App\Models\UserRestriction;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
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
        if (!empty($data['is_verified'])) {
            $data['email_verified_at'] = now();
        } else {
            $data['email_verified_at'] = null;
        }
        unset($data['is_verified']);

        // Remove password from data if it's empty (not changed)
        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }
}
