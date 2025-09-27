<?php

namespace App\Filament\Resources;

use App\Enums\CurrencyType;
use App\Enums\PolicyTypes;
use App\Enums\PriceUnit;
use App\Filament\Forms\Components\ActiveToggle;
use App\Filament\Helpers\TableActions;
use App\Filament\Resources\PricingPolicyResource\Pages;
use App\Filament\Tables\Columns\ActiveToggleColumn;
use App\Filament\Tables\Columns\Timestamps;
use App\Models\PricingPolicy;
use Filament\Forms\Components\{Select, TextInput, Grid};
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
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
                Grid::make('2')
                    ->schema([
                        Select::make('policy_type')
                            ->label('Policy Type')
                            ->options(PolicyTypes::class)
                            ->default(PolicyTypes::WEIGHT->value)
                            ->required(),
                        Select::make('currency')
                            ->label('Currency')
                            ->options(CurrencyType::class)
                            ->default(CurrencyType::SYRIA->value),
                    ]),
                Grid::make('2')
                    ->schema([
                        Select::make('price_unit')
                            ->label('Price Unit')
                            ->options(PriceUnit::class)
                            ->default(PriceUnit::KG)
                            ->required(),
                        TextInput::make('price')
                            ->label('Price')
                            ->required()
                            ->numeric()
                            ->rule('decimal:0,2')
                            ->prefixIcon('heroicon-o-currency-dollar') // أيقونة دولار بدلاً من نص
                            ->helperText('Enter the base price for this policy.')
                            ->reactive()
                            ->afterStateHydrated(function ($set, $state, $get) {
                                $currency = $get('currency') ?? 'USD';
                                $set('price', number_format((float) $state, 2, '.', ''));
                            }),
                    ]),
                Grid::make('2')
                    ->make([
                        TextInput::make('limit_min')
                            ->numeric()
                            ->default(null),
                        TextInput::make('limit_max')
                            ->numeric()
                            ->default(null),
                    ]),
                Grid::make('1')
                    ->schema([
                        ActiveToggle::make('is_active', 'Is Active ?'),

                    ]),
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
                ActiveToggleColumn::make('is_active'),
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
