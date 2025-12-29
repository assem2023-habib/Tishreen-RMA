<?php

namespace App\Filament\Tables\Actions;

use App\Models\BranchRoute;
use Filament\Tables\Actions\Action;
use App\Support\SharedNotification as Notification;

class MarkAsArrivedAction
{
    public static function make(): Action
    {
        return Action::make('markAsArrived')
            ->label('Mark as Arrived')
            ->icon('heroicon-s-check')
            ->color('success')
            ->requiresConfirmation()
            ->action(function (BranchRoute $record) {
                // Get all parcels related to this route
                $parcels = $record->parcels;

                foreach ($parcels as $parcel) {
                    // Prepare SMS message
                    $message = "Your parcel has arrived at the destination. Tracking code: {$parcel->tracking_number}";

                    // Example: call your SMS service
                    // SMSService::send($parcel->reciver_phone, $message);

                    // Record history
                    // $parcel->parcelsHistories()->create([
                    //     'status' => 'arrived',
                    //     'notes' => $message,
                    // ]);
                }

                // Show success notification
                Notification::make()
                    ->title('All related parcels have been updated!')
                    ->success()
                    ->send();
            });
    }
}
