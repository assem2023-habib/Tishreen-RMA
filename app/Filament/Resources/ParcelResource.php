<?php

namespace App\Filament\Resources;

use App\Enums\CurrencyType;
use App\Enums\ParcelStatus;
use App\Enums\SenderType;
use App\Filament\Resources\ParcelResource\Pages;
use App\Filament\Resources\ParcelResource\RelationManagers;
use App\Models\{User, PricingPolicy, Parcel, GuestUser, City, BranchRoute};
use Filament\Forms;

use Filament\Forms\Components\Wizard\Step;

use Filament\Forms\Components\{
    DatePicker,
    Grid,
    Select,
    TextInput,
    Toggle,
    Wizard
};
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ParcelResource extends Resource
{
    protected static ?string $model = Parcel::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Parcels';
    protected static ?int $navigationSort = 1;
    protected static bool $shouldRegisterNavigation = true;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    // select the sender type user - guest
                    Step::make('Choose Sender Type')
                        ->schema(
                            [
                                Select::make('sender_type')
                                    ->label('Sender Type')
                                    ->options(
                                        [
                                            User::class => SenderType::AUTHENTICATED_USER->value,
                                            GuestUser::class => SenderType::GUEST_USER->value,
                                        ],
                                    )
                                    ->reactive()
                                    ->required(),
                            ],
                        ),
                    // select sender account 
                    Step::make('Sender Authenticated Details')
                        ->label('Sender Details')
                        ->columns(1)
                        ->schema(
                            [
                                Select::make('sender_id')
                                    ->label("Sender")
                                    ->options(function () {
                                        return User::select('id', 'user_name', 'email')
                                            ->get()
                                            ->mapWithKeys(function ($user) {
                                                return [$user->id => $user->user_name];
                                            });
                                    })
                            ],
                        )
                        ->visible(fn(callable $get) => $get('sender_type') === User::class),

                    // parcel details for guest
                    Step::make('Sender Guest Details')
                        ->label('Sender Details')
                        ->columns(2)
                        ->schema(
                            [
                                TextInput::make('guest_first_name')
                                    ->label('First Name')
                                    ->required()
                                    ->visible(self::getVisible()),
                                TextInput::make('guest_last_name')
                                    ->label('Last Name')
                                    ->required()
                                    ->visible(self::getVisible()),
                                TextInput::make('guest_phone')
                                    ->label('Phone')
                                    ->required()
                                    ->visible(self::getVisible()),
                                TextInput::make('guest_address')
                                    ->label('Address')
                                    ->required()
                                    ->visible(self::getVisible()),
                                Select::make('guest_city_id')
                                    ->label('City')
                                    ->options(function () {
                                        return City::select('id', 'en_name')
                                            ->get()
                                            ->mapWithKeys(function ($city) {
                                                return [$city->id => $city->en_name];
                                            });
                                    })
                                    ->visible(self::getVisible()),
                                TextInput::make('guest_national_number')
                                    ->label('National Number')
                                    ->required()
                                    ->maxLength(11)
                                    ->minLength(11)
                                    ->ValidationMessages(
                                        [
                                            'required' => 'this filed was required',
                                        ]
                                    ),
                                DatePicker::make('birthday')
                                    ->label('Birthday')
                                    ->native(false),

                            ],
                        )
                        ->visible(self::getVisible()),


                    // ---------- paracel details for user 
                    Step::make('Parcel Details')
                        ->columns(2)
                        ->schema([
                            Select::make('route_id')
                                ->label('Branches Route')
                                ->options(function () {
                                    return BranchRoute::select('id', 'from_branch_id', 'to_branch_id')
                                        ->get()
                                        ->mapWithKeys(function ($branchRoute) {
                                            return [$branchRoute->id => $branchRoute->fromBranch->branch_name . " --> " . $branchRoute->toBranch->branch_name];
                                        });
                                }),

                            Forms\Components\TextInput::make('reciver_name')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('reciver_address')
                                ->required()
                                ->maxLength(255),

                            Forms\Components\TextInput::make('reciver_phone')
                                ->tel()
                                ->required()
                                ->maxLength(255),

                            Select::make('parcel_status')
                                ->label('Parcel Status')
                                ->options(ParcelStatus::class)
                                ->enum(ParcelStatus::class)
                                ->default(ParcelStatus::PENDING->value),

                            TextInput::make('tracking_number')
                                ->label('Tracking Number : ')
                                ->dehydrated(false)
                                ->disabled(),

                            // hussein update : 
                            // ---------- cost detial -----------
                            Forms\Components\TextInput::make('weight')
                                ->required()
                                ->numeric()
                                ->reactive() //
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    if ($state && $get('price_policy_id')) {
                                        $price = PricingPolicy::find($get('price_policy_id'))?->price ?? 0;
                                        $set('cost', $state * $price);
                                    }
                                }),

                            Select::make('price_policy_id')
                                ->label('Price Policy')
                                ->options(function () {
                                    return PricingPolicy::select('id', 'price', 'price_unit', 'currency')
                                        ->get()
                                        ->mapWithKeys(function ($pricingPolicy) {
                                            return [$pricingPolicy->id => 'price : ' . $pricingPolicy->price . ', price unit : ' . $pricingPolicy->price_unit];
                                        });
                                })
                                ->reactive() // for tracking updates 
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    if ($state && $get('weight')) {
                                        $price = PricingPolicy::find($state)?->price ?? 0;
                                        $set('cost', $get('weight') * $price);
                                    }
                                }),

                            Forms\Components\TextInput::make('cost')
                                ->readOnly()
                                ->numeric()
                                ->prefix('$')
                                ->dehydrateStateUsing(
                                    fn($state, $get) =>
                                    $get('weight') && $get('price_policy_id')
                                        ? $get('weight') * PricingPolicy::find($get('price_policy_id'))->price
                                        : 0
                                )
                                ->afterStateHydrated(function ($set, $get) {
                                    if ($get('weight') && $get('price_policy_id')) {
                                        $set('cost', $get('weight') * PricingPolicy::find($get('price_policy_id'))->price);
                                    }
                                }),

                            Grid::make(1)->schema([
                                Toggle::make('is_paid')
                                    ->label('Paid')
                                    ->onIcon('heroicon-o-check-circle')
                                    ->offIcon('heroicon-o-no-symbol')
                                    ->onColor('success')
                                    ->offColor('danger'),
                            ]),
                        ]),
                ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sender_name')
                    ->label('Sender')
                    ->getStateUsing(function ($record) {
                        if ($record->sender_type === SenderType::AUTHENTICATED_USER) {

                            $user = User::findOrFail($record->sender_id);
                            return $user->user_name;
                        } else if ($record->sender_type === SenderType::GUEST_USER) {
                            $guestUser = GuestUser::findOrFail($record->sender_id);

                            return $guestUser->first_name . $guestUser->last_name;
                        }
                        return '-';
                    }),
                TextColumn::make('sender_type')
                    ->label('Sender Type')
                    ->badge()
                    ->color(
                        fn($state) =>
                        $state === SenderType::GUEST_USER ? 'success' : 'danger'
                    ),
                TextColumn::make('routeLabel')
                    ->label('Route')
                    ->getStateUsing(fn($record) => $record->routeLabel),
                TextColumn::make('reciver_name')
                    ->searchable(),
                TextColumn::make('reciver_address')
                    ->searchable(),
                TextColumn::make('reciver_phone')
                    ->searchable(),
                TextColumn::make('weight')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost')
                    ->money()
                    ->sortable(),
                ToggleColumn::make('is_paid')
                    ->onIcon('')
                    ->offIcon('')
                    ->onColor('success')
                    ->offColor('danger')
                    ->disabled(),
                TextColumn::make('parcel_status')
                    ->label('Parcel Status')
                    ->badge()
                    ->color(fn(string $state) =>
                    match ($state) {
                        ParcelStatus::PENDING->value => 'danger',
                        ParcelStatus::CONFIRMED->value => 'success',
                        ParcelStatus::IN_TRANSIT->value => 'danger',
                        ParcelStatus::READY_FOR_PICKUP->value => 'success',
                        ParcelStatus::DELIVERED->value => 'secondary',
                        ParcelStatus::CANCELED->value => 'primary',
                        ParcelStatus::FAILED->value => 'secondary',
                        ParcelStatus::RETURNED->value => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('tracking_number')
                    ->searchable(),
                TextColumn::make('pricePolicy.policy_type')
                    ->label('Policy Type')
                    ->sortable(),
                TextColumn::make('pricePolicy.price')
                    ->label('Policy Price')
                    ->getStateUsing(function ($record) {
                        if ($record->pricePolicy) {
                            $icon = CurrencyType::from($record->pricePolicy->currency)->currencyIcon();
                            return $icon . ' ' . number_format($record->pricePolicy->price, 2);
                        }
                        return '-';
                    })
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
            'index' => Pages\ListParcels::route('/'),
            'create' => Pages\CreateParcel::route('/create'),
            'edit' => Pages\EditParcel::route('/{record}/edit'),
        ];
    }
    private static function getVisible()
    {
        return fn(callable $get) => $get('sender_type') === GuestUser::class;
    }
}
