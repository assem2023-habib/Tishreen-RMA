<?php

namespace App\Filament\Tables\Actions;

use App\Enums\ParcelStatus;
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
            });
    }
}
