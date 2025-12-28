<?php

namespace App\Filament\Resources;

use App\Enums\ParcelStatus;
use App\Filament\Helpers\TableActions;
use App\Filament\Resources\ShipmentResource\Pages;
use App\Filament\Resources\ShipmentResource\RelationManagers;
use App\Filament\Tables\Actions\ArriveShipmentAction;
use App\Filament\Tables\Actions\DepartShipmentAction;
use App\Filament\Tables\Actions\ManageShipmentParcelsAction;
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
                Select::make('trucks')
                    ->relationship('trucks', 'truck_number')
                    ->multiple()
                    ->preload()
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
                TextColumn::make('trucks.truck_number')
                    ->label('Trucks')
                    ->badge(),
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
                DepartShipmentAction::make(),
                ArriveShipmentAction::make(),
                ManageShipmentParcelsAction::make(),
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
