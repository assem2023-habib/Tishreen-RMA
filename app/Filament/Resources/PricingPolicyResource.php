<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PricingPolicyResource\Pages;
use App\Models\PricingPolicy;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PricingPolicyResource extends Resource
{
    protected static ?string $model = PricingPolicy::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = "Support & Information";

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('policy_type')
                    ->required(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('price_unit')
                    ->required(),
                TextInput::make('limit_min')
                    ->numeric()
                    ->default(null),
                TextInput::make('limit_max')
                    ->numeric()
                    ->default(null),
                TextInput::make('currency'),
                TextInput::make('is_active')
                    ->required()
                    ->numeric()
                    ->default(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('policy_type'),
                TextColumn::make('price')
                    ->money()
                    ->sortable(),
                TextColumn::make('price_unit'),
                TextColumn::make('limit_min')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('limit_max')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('currency'),
                TextColumn::make('is_active')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListPricingPolicies::route('/'),
            'create' => Pages\CreatePricingPolicy::route('/create'),
            'edit' => Pages\EditPricingPolicy::route('/{record}/edit'),
        ];
    }
}
