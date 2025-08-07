<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TruckResource\Pages;
use App\Filament\Resources\TruckResource\RelationManagers;
use App\Models\Employee;
use App\Models\Truck;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TruckResource extends Resource
{
    protected static ?string $model = Truck::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Transport';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    Select::make('driver_id')
                        ->label('Driver')
                        ->relationship('driver')
                        ->getOptionLabelFromRecordUsing(function (Employee $record): string {
                            return $record->user->first_name . " " . $record->user->last_name;
                        })
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    Forms\Components\TextInput::make('truck_number')
                        ->label('Truck National Number')
                        ->placeholder('please insert the truck identifier')
                        ->maxLength(9)
                        ->minLength(9)
                        ->required(),
                ]),
                Grid::make(2)->schema(
                    [
                        Forms\Components\TextInput::make('truck_model')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('capacity_per_kilo_gram')
                            ->label('Capacity')
                            ->placeholder('please insert capactiy per kilo gram')
                            ->required()
                            ->numeric(),
                    ],
                ),
                Grid::make(1)->schema(
                    [
                        Toggle::make('is_active')
                            ->label('Work?..')
                            ->onIcon('heroicon-o-check-circle')
                            ->offIcon('heroicon-o-no-symbol')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(1),
                        // ->extraAttributes(['order' => 3]),
                    ],
                ),
                // ->extraAttributes(['justify-content' => 'end']),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('driver_id')
                //     ->numeric()
                //     ->sortable(),
                TextColumn::make('driver.user.user_name')
                    ->label('Driver name')
                    ->searchable(),
                TextColumn::make('truck_number')
                    ->searchable(),
                TextColumn::make('truck_model')
                    ->searchable(),
                TextColumn::make('capacity_per_kilo_gram')
                    ->numeric()
                    ->sortable(),
                // TextColumn::make('is_active')
                //     ->numeric()
                //     ->sortable(),
                ToggleColumn::make('is_active')
                    ->onIcon('heroicon-o-check-circle')
                    ->offIcon('heroicon-o-no-symbol')
                    ->onColor('success')
                    ->offColor('danger'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
            'index' => Pages\ListTrucks::route('/'),
            'create' => Pages\CreateTruck::route('/create'),
            'edit' => Pages\EditTruck::route('/{record}/edit'),
        ];
    }
}
