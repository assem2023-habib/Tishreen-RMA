<?php

namespace App\Filament\Tables\Actions;

use App\Enums\ParcelStatus;
use App\Models\Shipment;
use Filament\Tables\Actions\Action;
use App\Support\SharedNotification as Notification;

class DepartShipmentAction
{
    public static function make(): Action
    {
        return Action::make('depart')
            ->label('Depart')
            ->icon('heroicon-o-paper-airplane')
            ->color('primary')
            ->visible(fn(Shipment $record) => $record->actual_departure_time === null)
            ->action(function (Shipment $record) {
                $record->update([
                    'actual_departure_time' => now(),
                ]);

                // Update all linked parcels status to IN_TRANSIT
                $parcelIds = $record->parcelAssignments()->pluck('parcel_id');
                \App\Models\Parcel::whereIn('id', $parcelIds)
                    ->where('parcel_status', ParcelStatus::READY_FOR_SHIPPING) // Only update parcels ready for shipping
                    ->get()
                    ->each(function ($parcel) {
                        $parcel->update([
                            'parcel_status' => ParcelStatus::IN_TRANSIT,
                        ]);
                    });

                Notification::make()
                    ->title('Shipment Departed')
                    ->body('Shipment marked as departed and linked ready parcels status updated to In Transit.')
                    ->success()
                    ->send();
            })
            ->requiresConfirmation();
    }
}
