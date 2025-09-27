<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParcelShipmentAssignmentResource\Pages;
use App\Filament\Resources\ParcelShipmentAssignmentResource\RelationManagers;
use App\Models\ParcelShipmentAssignment;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\{Builder, SoftDeletingScope};

class ParcelShipmentAssignmentResource extends Resource
{
    protected static ?string $model = ParcelShipmentAssignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?string $navigationGroup = "Parcels";
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('parcel_id')
                    ->relationship('parcel', 'id')
                    ->required(),
                Select::make('shipment_id')
                    ->relationship('shipment', 'id')
                    ->required(),
                Select::make('pick_up_confirmed_by_emp_id')
                    ->relationship('receivedByEmployee', 'id')
                    ->label('Received By'),
                DateTimePicker::make('pick_up_confirmed_date')
                    ->label('Received At'),
                Select::make('delivery_confirmed_by_emp_id')
                    ->relationship('deliveredByEmployee', 'id')
                    ->label('Delivered By'),
                DateTimePicker::make('delivery_confirmed_date')
                    ->label('Delivered At'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('parcel.sender_name')
                    ->label('Parcel'),
                TextColumn::make('shipment.branchRouteDay.branchRoute.route_label')
                    ->label('Shipment'),
                TextColumn::make('receivedByEmployee.user.user_name')
                    ->label('Received By'),
                TextColumn::make('pick_up_confirmed_date')
                    ->dateTime()
                    ->label('Received At'),
                TextColumn::make('deliveredByEmployee.user.user_name')
                    ->label('Delivered  By'),
                TextColumn::make('delivery_confirmed_date')
                    ->dateTime()
                    ->label('Delivered At'),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            'index' => Pages\ListParcelShipmentAssignments::route('/'),
            'create' => Pages\CreateParcelShipmentAssignment::route('/create'),
            'edit' => Pages\EditParcelShipmentAssignment::route('/{record}/edit'),
        ];
    }
}
