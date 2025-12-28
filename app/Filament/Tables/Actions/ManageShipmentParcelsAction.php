<?php

namespace App\Filament\Tables\Actions;

use App\Enums\ParcelStatus;
use App\Models\Shipment;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class ManageShipmentParcelsAction
{
    public static function make(): Action
    {
        return Action::make('manageParcels')
            ->label('Manage Parcels Status')
            ->icon('heroicon-o-adjustments-horizontal')
            ->color('warning')
            ->form([
                Select::make('new_status')
                    ->label('New Status for all linked parcels')
                    ->options(ParcelStatus::class)
                    ->required(),
            ])
            ->action(function (Shipment $record, array $data) {
                $parcelIds = $record->parcelAssignments()->pluck('parcel_id');
                \App\Models\Parcel::whereIn('id', $parcelIds)->get()->each(function ($parcel) use ($data) {
                    $parcel->update([
                        'parcel_status' => $data['new_status'],
                    ]);
                });

                Notification::make()
                    ->title('Parcels Updated')
                    ->body('All parcels linked to this shipment have been updated to ' . $data['new_status'])
                    ->success()
                    ->send();
            })
            ->requiresConfirmation();
    }
}
