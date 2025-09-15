<?php

namespace App\Filament\Resources;

use App\Enums\AuthorizationStatus;
use App\Enums\SenderType;
use App\Filament\Resources\ParcelAuthorizationResource\Pages;
use App\Filament\Resources\ParcelAuthorizationResource\RelationManagers;
use App\Models\City;
use App\Models\GuestUser;
use App\Models\Parcel;
use App\Models\ParcelAuthorization;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\{Wizard, Select, TextInput};
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParcelAuthorizationResource extends Resource
{
    protected static ?string $model = ParcelAuthorization::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Parcels';
    protected static ?int $navigationSort = 4;
    protected static bool $shouldRegisterNavigation = true;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Wizard::make([
                    Step::make('Choose Sender')
                        ->schema([
                            Select::make('user_id')
                                ->label('User')
                                ->options(function () {
                                    return User::select('id', 'user_name')
                                        ->get()
                                        ->mapWithKeys(
                                            function ($user) {
                                                return [$user->id => $user->user_name];
                                            }
                                        );
                                }),
                        ]),
                    Step::make('Choose Reciver Type')
                        ->schema(
                            [
                                Select::make('authorized_user_type')
                                    ->label('Sender Type')
                                    ->options(
                                        [
                                            User::class => SenderType::AUTHENTICATED_USER->value,
                                            GuestUser::class => SenderType::GUEST_USER->value,
                                        ],
                                    )
                                    ->default(User::class)
                                    ->reactive()
                                    ->required(),
                            ],
                        ),
                    Step::make('Reciver Authenticated Details')
                        ->label('Reciver Details')
                        ->columns(1)
                        ->schema(
                            [
                                Select::make('authorized_user_id')
                                    ->label("Reciver")
                                    ->options(function (callable $get) {
                                        $senderId = $get('user_id');
                                        return User::select('id', 'user_name', 'email')
                                            ->whereNot('id', $senderId)
                                            ->get()
                                            ->mapWithKeys(function ($user) {
                                                return [$user->id => $user->user_name];
                                            });
                                    })
                            ],
                        )
                        ->visible(self::getVisibleForUser()),
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
                    Step::make('Delegation Data')
                        ->schema([
                            Grid::make()
                                ->schema([
                                    Select::make('parcel_id')
                                        ->required()
                                        ->options(function (callable $get) {
                                            $sender_id = $get('user_id');
                                            return Parcel::select('id', 'sender_id', 'sender_type', 'route_id', 'reciver_name')
                                                ->where('sender_id', $sender_id)
                                                // ->with('route.fromBranch', 'route.toBranch')
                                                ->where('sender_type', SenderType::AUTHENTICATED_USER->value)
                                                ->get()
                                                ->mapWithKeys(
                                                    function ($parcel) {
                                                        return [$parcel->id => 'reciver name: ' . $parcel->reciver_name . ' , route : ' . $parcel->routeLabel];
                                                    }
                                                );
                                        }),
                                ]),
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('authorized_code')
                                        ->disabled(),
                                    Select::make('authorized_status')
                                        ->label('Authorized Status')
                                        ->options(AuthorizationStatus::values())
                                        ->default('Pending')
                                        ->required(),
                                ]),
                            Grid::make(3)->schema([
                                DateTimePicker::make('generated_at')
                                    ->default(now())
                                    ->required(),
                                DateTimePicker::make('expired_at')
                                    ->default(state: fn() => Carbon::now()->addDays(7)),
                                DateTimePicker::make('used_at'),
                            ]),
                            TextInput::make('cancellation_reason')
                                ->maxLength(255)
                                ->default(null),
                        ])
                ])
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.user_name')
                    ->sortable(),
                TextColumn::make('parcel.reciver_name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('authorizedUser.first_name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('authorized_user_type'),
                TextColumn::make('authorized_code')
                    ->searchable(),
                TextColumn::make('authorized_status')
                    ->sortable()
                    ->badge()
                    ->color(
                        fn($state) =>
                        match ($state) {
                            AuthorizationStatus::PENDING->value => 'danger',
                            AuthorizationStatus::ACTIVE->value => 'success',
                            AuthorizationStatus::EXPIRED->value => 'danger',
                            AuthorizationStatus::USED->value => 'danger',
                            AuthorizationStatus::CANCELLED->value => 'success',
                            default => 'gray',
                        }
                    ),
                TextColumn::make('generated_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('expired_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('used_at')
                    ->dateTime()
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
            'index' => Pages\ListParcelAuthorizations::route('/'),
            'create' => Pages\CreateParcelAuthorization::route('/create'),
            'edit' => Pages\EditParcelAuthorization::route('/{record}/edit'),
        ];
    }
    private static function getVisible()
    {
        return fn(callable $get) => $get('authorized_user_type') === GuestUser::class;
    }
    private static function getVisibleForUser()
    {
        return fn(callable $get) => $get('authorized_user_type') === User::class;
    }
}
