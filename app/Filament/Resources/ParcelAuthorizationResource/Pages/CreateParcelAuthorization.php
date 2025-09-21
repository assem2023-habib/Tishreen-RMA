<?php

namespace App\Filament\Resources\ParcelAuthorizationResource\Pages;

use App\Enums\SenderType;
use App\Filament\Resources\ParcelAuthorizationResource;
use App\Models\GuestUser;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateParcelAuthorization extends CreateRecord
{
    protected static string $resource = ParcelAuthorizationResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        $authoriztion = $this->record;
        return Notification::make()
            ->success()
            ->title("Parcel Created : {$authoriztion->authorized_code}")
            ->body("The parcel with status "
                . $authoriztion->authorized_status
                . " has been created successfully at "
                . $authoriztion->generated_at
                . " and expired at "
                . $authoriztion->expired_at
                . " .");
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {

        if ($data['authorized_user_type'] === GuestUser::class) {
            $guestUser = $this->saveGuestUser($data);
            $data['authorized_user_id'] = $guestUser->id;
            $data['authorized_user_type'] = SenderType::GUEST_USER->value;
        }
        if ($data['authorized_user_type'] === User::class) {
            $data['authorized_user_type'] = SenderType::AUTHENTICATED_USER->value;
        }



        return $data;
    }
    private function saveGuestUser($data)
    {
        return  GuestUser::create([
            'first_name' => $data['guest_first_name'],
            'last_name' => $data['guest_last_name'],
            'phone' => $data['guest_phone'],
            'address' => $data['guest_address'],
            'city_id' => $data['guest_city_id'],
            'national_number' => $data['guest_national_number'],
            'birthday' => $data['birthday'],
        ]);
    }
}
