<?php

namespace App\Filament\Tables\Actions;

use App\Enums\ParcelStatus;
use App\Models\Shipment;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

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

                        // Send Dashboard Notification to Sender
                        $sender = $parcel->sender;
                        if ($sender instanceof \App\Models\User) {
                            $trackingNumber = $parcel->tracking_number ?? '---';
                            Notification::make()
                                ->title('الطرد في الطريق')
                                ->body("تحركت الشحنة التي تحتوي على طردك ذو الرقم المرجعي ($trackingNumber). طردك الآن في حالة شحن (In Transit).")
                                ->info()
                                ->icon('heroicon-o-paper-airplane')
                                ->sendToDatabase($sender)
                                ->broadcast($sender);
                        }
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
