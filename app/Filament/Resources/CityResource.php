<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\TableActions;
use App\Filament\Resources\CityResource\Pages;
use App\Filament\Resources\CityResource\RelationManagers;
use App\Filament\Tables\Columns\Timestamps;
use App\Models\City;
use App\Models\Country;
use Filament\Forms;
use Filament\Forms\Components\{Select, TextInput};
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Geographical Data';
    protected static ?int $navigationSort = 2;

    protected static bool $shouldRegisterNavigation = true;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('country_id')
                    ->required()
                    ->options(Country::all()->pluck('en_name', 'id')),
                TextInput::make('en_name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('ar_name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('country.en_name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('en_name')
                    ->searchable(),
                TextColumn::make('ar_name')
                    ->searchable(),
                ...Timestamps::make(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            RelationManagers\BranchesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
