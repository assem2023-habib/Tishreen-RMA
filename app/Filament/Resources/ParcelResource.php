<?php

namespace App\Filament\Resources;

use App\Enums\{ParcelStatus, SenderType};
use App\Filament\Forms\Components\{ActiveToggle, PhoneNumber, LocationSelect};
use App\Filament\Helpers\TableActions;
use App\Filament\Resources\ParcelResource\Pages;
use App\Filament\Tables\Actions\{ConfirmParcelAction, ViewGuestSenderAction};
use App\Filament\Tables\Columns\ActiveToggleColumn;
use App\Models\{User, Parcel, BranchRoute};
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\{
    DatePicker,
    Grid,
    Select,
    TextInput,
    Wizard
};

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
                                        SenderType::class
                                    )
                                    ->reactive()
                                    ->default(SenderType::GUEST_USER->value)
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
                        ->visible(self::getVisible(SenderType::AUTHENTICATED_USER->value)),

                    // parcel details for guest
                    Step::make('Sender Guest Details')
                        ->label('Sender Details')
                        ->columns(2)
                        ->schema(
                            [
                                TextInput::make('guest_first_name')
                                    ->label('First Name')
                                    ->required(),
                                TextInput::make('guest_last_name')
                                    ->label('Last Name')
                                    ->required()
                                    ->visible(self::getVisible()),
                                PhoneNumber::make('guest_phone', 'Phone'),
                                TextInput::make('guest_address')
                                    ->label('Address')
                                    ->required(),
                                // Select::make('guest_city_id')
                                //     ->label('City')
                                //     ->options(function () {
                                //         return City::select('id', 'en_name')
                                //             ->get()
                                //             ->mapWithKeys(function ($city) {
                                //                 return [$city->id => $city->en_name];
                                //             });
                                //     }),
                                LocationSelect::make('guest_city_id', 'guest_country_id', 'City', 'Country'),
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

                            TextInput::make('reciver_name')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('reciver_address')
                                ->required()
                                ->maxLength(255),

                            PhoneNumber::make('reciver_phone', 'Reciver Phone'),

                            Select::make('parcel_status')
                                ->label('Parcel Status')
                                ->options(ParcelStatus::class)
                                ->enum(ParcelStatus::class)
                                ->default(ParcelStatus::CONFIRMED->value),

                            TextInput::make('tracking_number')
                                ->label('Tracking Number : ')
                                ->dehydrated(false)
                                ->disabled(),

                            // ---------- cost detial -----------
                            TextInput::make('weight')
                                ->required()
                                ->numeric()
                                ->reactive(),

                            TextInput::make('cost')
                                ->readOnly()
                                ->numeric()
                                ->prefix('$'),

                            Grid::make(1)->schema([
                                ActiveToggle::make('is_paid', 'Paid'),
                            ]),
                        ])
                ])
                    ->columnSpanFull()
                    ->submitAction(self::creteButton()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sender.name')
                    ->label('Sender'),
                TextColumn::make('sender_type')
                    ->label('Sender Type')
                    ->badge()
                    ->color(
                        fn($state) =>
                        $state === SenderType::GUEST_USER ? 'success' : 'danger'
                    ),
                TextColumn::make('route_label')
                    ->label('Route'),
                TextColumn::make('reciver_name')
                    ->searchable(),
                TextColumn::make('reciver_address')
                    ->searchable(),
                TextColumn::make('reciver_phone')
                    ->searchable(),
                TextColumn::make('weight')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('cost')
                    ->money('SYR')
                    ->sortable(),
                ActiveToggleColumn::make('is_paid')
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
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tracking_number')
                    ->badge()
                    ->searchable(),

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
                ConfirmParcelAction::make(),
                ViewGuestSenderAction::make(),
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
            'index' => Pages\ListParcels::route('/'),
            'create' => Pages\CreateParcel::route('/create'),
            'edit' => Pages\EditParcel::route('/{record}/edit'),
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    private static function getVisible($senderType = SenderType::GUEST_USER->value)
    {
        return fn(callable $get) => $get('sender_type') === $senderType;
    }
    private static function creteButton(): HtmlString
    {
        return new HtmlString(
            "<button style=\"
                border-radius: 12px;
                padding: 10px 20px;
                font-weight: 600;
                font-size: 14px;
                color: #fff;
                background: linear-gradient(135deg, #ff7e5f, #feb47b);
                border: none;
                cursor: pointer;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            \"
            onmouseover=\"
                this.style.transform='translateY(-2px)';
                this.style.boxShadow='0 4px 10px rgba(0,0,0,0.2)';
            \"
            onmouseout=\"
                this.style.transform='translateY(0)';
                this.style.boxShadow='none';
            \">
                Create Parcel
            </button>"
        );
    }
}
