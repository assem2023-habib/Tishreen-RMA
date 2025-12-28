<?php

namespace App\Filament\Tables\Actions;

use App\Enums\AuthorizationStatus;
use App\Enums\ParcelStatus;
use App\Models\ParcelAuthorization;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class ConfirmAuthReceiptAction
{
    public static function make(): Action
    {
        return Action::make('confirmReceipt')
            ->label('Confirm Receipt')
            ->icon('heroicon-o-check-badge')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Confirm Parcel Delivery via Authorization')
            ->modalDescription('Are you sure you want to mark this parcel as delivered to the authorized person?')
            ->visible(fn (ParcelAuthorization $record): bool => 
                $record->authorized_status !== AuthorizationStatus::USED->value &&
                $record->authorized_status !== AuthorizationStatus::CANCELLED->value &&
                $record->authorized_status !== AuthorizationStatus::EXPIRED->value
            )
            ->action(function (ParcelAuthorization $record): void {
                DB::transaction(function () use ($record) {
                    // 1. Update Authorization Status to USED
                    $record->update([
                        'authorized_status' => AuthorizationStatus::USED->value,
                        'used_at' => now(),
                    ]);

                    // 2. Update Linked Parcel Status to DELIVERED
                    if ($record->parcel) {
                        $record->parcel->update([
                            'parcel_status' => ParcelStatus::DELIVERED,
                        ]);
                    }
                });

                Notification::make()
                    ->title('Receipt Confirmed')
                    ->body('The parcel has been marked as Delivered and the authorization is now marked as Used.')
                    ->success()
                    ->send();
            });
    }
}
