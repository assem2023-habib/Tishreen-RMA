<?php

namespace App\Filament\Resources;

use App\Enums\ParcelStatus;
use App\Enums\SenderType;
use App\Filament\Resources\ParcelResource\Pages;
use App\Filament\Resources\ParcelResource\RelationManagers;
use App\Models\BranchRoute;
use App\Models\City;
use App\Models\GuestUser;
use App\Models\Parcel;
use App\Models\PricingPoliciy;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParcelResource extends Resource
{
    protected static ?string $model = Parcel::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Parcels';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Grid::make(2)->schema([
                // MorphToSelect::make('sender')
                //     ->types([
                //         MorphToSelect\Type::make(User::class)
                //             ->titleAttribute('first_name'),
                //         MorphToSelect\Type::make(GuestUser::class)
                //             ->titleAttribute('first_name'),
                //     ])
                //     ->columnSpanFull()
                //     ->required(),

                // ]),
                // TextInput::make('sender_type')
                //     ->required(),
                // Forms\Components\TextInput::make('route_id')
                //     ->required()
                //     ->numeric(),
                Wizard::make([
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
                                        // SenderType::class
                                    )
                                    ->reactive()
                                    ->default(SenderType::AUTHENTICATED_USER->value)
                                    ->required(),
                            ],
                        ),
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
                    Step::make('Sender Guest Details')
                        ->label('Sender Details')
                        ->columns(2)
                        ->schema(
                            [
                                TextInput::make('guest_first_name')
                                    ->label('First Name')
                                    ->required()
                                    // ->visible(fn(callable $get) => $get('sender_type') === GuestUser::class)
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
                                    // ->digits(11)
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
                            Forms\Components\TextInput::make('weight')
                                ->required()
                                ->numeric(),
                            Forms\Components\TextInput::make('cost')
                                ->required()
                                ->numeric()
                                ->prefix('$'),
                            // Forms\Components\TextInput::make('is_paid')
                            //     ->required()
                            //     ->numeric()
                            //     ->default(0),

                            // Forms\Components\TextInput::make('parcel_status')
                            //     ->required(),
                            Select::make('parcel_status')
                                ->label('Parcel Status')
                                ->options(ParcelStatus::class)
                                ->enum(ParcelStatus::class)
                                ->default(ParcelStatus::PENDING),
                            // Forms\Components\TextInput::make('tracking_number')
                            //     ->required()
                            //     ->maxLength(255),
                            TextInput::make('tracking_number')
                                ->disabled(),
                            Select::make('price_policy')
                                ->label('Price Policy')
                                ->options(function () {
                                    return PricingPoliciy::select('id', 'price', 'price_unit', 'currency')
                                        ->get()
                                        ->mapWithKeys(function ($pricingPolicy) {
                                            return [$pricingPolicy->id => 'price : ' . $pricingPolicy->price . ', price unit : ' . $pricingPolicy->price_unit];
                                        });
                                }),
                            Grid::make(1)->schema([
                                Toggle::make('is_paid')
                                    ->label('Paid')
                                    ->onIcon('heroicon-o-check-circle')
                                    ->offIcon('heroicon-o-no-symbol')
                                    ->onColor('success')
                                    ->offColor('danger'),
                                // ->tooltip('if you want to pay the parcel cost activate this toggle button!....'),
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
                Tables\Columns\TextColumn::make('sender_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sender_type'),
                Tables\Columns\TextColumn::make('route_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reciver_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reciver_address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reciver_phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('weight')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_paid')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('parcel_status'),
                Tables\Columns\TextColumn::make('tracking_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('price_policy')
                    ->numeric()
                    ->sortable(),
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
