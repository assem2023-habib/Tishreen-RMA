<?php

namespace App\Filament\Tables\Actions;

use App\Enums\ParcelStatus;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class ConfirmParcelAction
{
    public static function make(): Action
    {
        return Action::make('confirmParcel')
            ->label('Confirm Parcel Reception at Branch')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->requiresConfirmation()
            ->visible(fn($record) => $record->parcel_status === ParcelStatus::PENDING->value)
            ->action(function ($record) {
                $record->update([
                    'parcel_status' => ParcelStatus::CONFIRMED->value
                ]);

                // Send Dashboard Notification to Sender
                $sender = $record->sender;
                if ($sender instanceof User) {
                    $trackingNumber = $record->tracking_number ?? '---';
                    Notification::make()
                        ->title('تم استلام الطرد في الفرع')
                        ->body("تم استلام طردك ذو الرقم المرجعي ($trackingNumber) في الفرع بنجاح وهو الآن قيد المعالجة.")
                        ->success()
                        ->icon('heroicon-o-building-office')
                        ->sendToDatabase($sender);
                }
            });
    }
}
