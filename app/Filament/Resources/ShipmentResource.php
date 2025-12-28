<?php

namespace App\Filament\Resources;

use App\Enums\ParcelStatus;
use App\Filament\Helpers\TableActions;
use App\Filament\Resources\ShipmentResource\Pages;
use App\Filament\Resources\ShipmentResource\RelationManagers;
use App\Models\Shipment;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShipmentResource extends Resource
{
    protected static ?string $model = Shipment::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = "Transport";
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('branch_route_day_id')
                    ->relationship('branchRouteDay', 'id')
                    ->label('Route Schedule')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->day_of_week} | {$record->branchRoute->route_label} ({$record->estimated_departur_time} - {$record->estimated_arrival_time})")
                    ->required(),
                Select::make('truck_id')
                    ->relationship('truck', 'truck_number')
                    ->required(),
                DateTimePicker::make('actual_departure_time')
                    ->label('Departure Time'),
                DateTimePicker::make('actual_arrival_time')
                    ->label('Arrival Time'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('branchRouteDay.branchRoute.route_label')
                    ->label('Branch Route')
                    ->sortable(),
                TextColumn::make('truck.truck_number')
                    ->label('Truck'),
                TextColumn::make('actual_departure_time')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('actual_arrival_time')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('depart')
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
                            ->where('parcel_status', ParcelStatus::PENDING) // Only update pending parcels
                            ->get()
                            ->each(function ($parcel) {
                                $parcel->update([
                                    'parcel_status' => ParcelStatus::IN_TRANSIT,
                                ]);
                            });

                        Notification::make()
                            ->title('Shipment Departed')
                            ->body('Shipment marked as departed and linked pending parcels status updated to In Transit.')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),

                Tables\Actions\Action::make('arrive')
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
                            ->body('Shipment marked as arrived and linked in-transit parcels status updated to Ready For Pickup.')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),

                Tables\Actions\Action::make('manageParcels')
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
                    ->requiresConfirmation(),

                TableActions::default(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShipments::route('/'),
            'create' => Pages\CreateShipment::route('/create'),
            'edit' => Pages\EditShipment::route('/{record}/edit'),
        ];
    }
}
