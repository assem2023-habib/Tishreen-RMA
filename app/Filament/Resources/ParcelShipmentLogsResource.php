<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParcelShipmentLogsResource\Pages;
use App\Filament\Resources\ParcelShipmentLogsResource\RelationManagers;
use App\Models\ParcelShipmentLogs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParcelShipmentLogsResource extends Resource
{
    protected static ?string $model = ParcelShipmentLogs::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Parcels';
    protected static ?int $navigationSort = 3;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('parcel_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('pick_up_confirmed_by_emp_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('delivery_confirmed_by_emp_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('truck_id')
                    ->required()
                    ->numeric(),
                Forms\Components\DateTimePicker::make('pick_up_confiremd_date')
                    ->required(),
                Forms\Components\DateTimePicker::make('delivery_confirmed_date'),
                Forms\Components\DateTimePicker::make('assigned_truck_date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('parcel_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pick_up_confirmed_by_emp_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('delivery_confirmed_by_emp_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('truck_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pick_up_confiremd_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('delivery_confirmed_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assigned_truck_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListParcelShipmentLogs::route('/'),
            'create' => Pages\CreateParcelShipmentLogs::route('/create'),
            'edit' => Pages\EditParcelShipmentLogs::route('/{record}/edit'),
        ];
    }
}
