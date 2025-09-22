<?php

namespace App\Filament\Resources\ParcelResource\Pages;

use App\Enums\SenderType;
use App\Filament\Resources\ParcelResource;
use App\Models\PricingPolicy;
use App\Models\User;
use App\Models\{GuestUser, Parcel};
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use League\Csv\Serializer\CastToArray;

class CreateParcel extends CreateRecord
{
    protected static string $resource = ParcelResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getFormActions(): array
    {
        return []; // صحيح
    }
    protected function getCreatedNotification(): ?Notification
    {
        $parcel = $this->record;
        return Notification::make()
            ->success()
            ->title("Parcel Created : {$parcel->tracking_number}")
            ->body("The parcel with cost {$parcel->cost} and tracking number {$parcel->tracking_number} has been created successfully.");
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {

        if ($data['sender_type'] === SenderType::GUEST_USER->value) {
            $guestUser = $this->saveGuestUser($data);
            $data['sender_id'] = $guestUser->id;
            return $data;
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
