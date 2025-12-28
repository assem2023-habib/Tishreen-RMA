<?php

namespace App\Filament\Tables\Actions;

use App\Enums\ParcelStatus;
use App\Models\Shipment;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class ArriveShipmentAction
{
    public static function make(): Action
    {
        return Action::make('arrive')
            ->label('Arrive')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->visible(fn(Shipment $record) => $record->actual_departure_time !== null && $record->actual_arrival_time === null)
            ->action(function (Shipment $record) {
                $record->update([
                    'actual_arrival_time' => now(),
                ]);

                // Update all linked parcels status to READY_FOR_PICKUP
                $parcelIds = $record->parcelAssignments()->pluck('parcel_id');
                \App\Models\Parcel::whereIn('id', $parcelIds)
                    ->where('parcel_status', ParcelStatus::IN_TRANSIT) // Only update in-transit parcels
                    ->get()
                    ->each(function ($parcel) {
                        $parcel->update([
                            'parcel_status' => ParcelStatus::READY_FOR_PICKUP,
                        ]);
                    });

                Notification::make()
                    ->title('Shipment Arrived')
                    ->body('Shipment marked as arrived and linked in-transit parcels status updated to Ready For Pickup (Arrived at Branch).')
                    ->success()
                    ->send();
            })
            ->requiresConfirmation();
    }
}
