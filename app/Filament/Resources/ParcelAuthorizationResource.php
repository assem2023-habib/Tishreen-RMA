<?php

namespace App\Filament\Resources;

use App\Enums\{AuthorizationStatus, SenderType};
use App\Filament\Forms\Components\{LocationSelect, PhoneNumber, NationalNumber};
use App\Filament\Helpers\TableActions;
use App\Filament\Resources\ParcelAuthorizationResource\Pages;
use App\Filament\Tables\Actions\ConfirmAuthReceiptAction;
use App\Models\{Parcel, User, ParcelAuthorization};
use App\Trait\HasSenderVisibility;
use Carbon\Carbon;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\{Wizard, Select, TextInput, Grid, DateTimePicker, DatePicker};
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ParcelAuthorizationResource extends Resource
{
    use HasSenderVisibility;
    protected static ?string $model = ParcelAuthorization::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
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
                                ->options(
                                    User::pluck('user_name', 'id')
                                ),
                        ]),
                    Step::make('Choose Reciver Type')
                        ->schema(
                            [
                                Select::make('authorized_user_type')
                                    ->label('Sender Type')
                                    ->options(
                                        SenderType::class
                                    )
                                    ->default(SenderType::AUTHENTICATED_USER->value)
                                    ->reactive()
                                    ->required(),
                            ],
                        ),
                    Step::make('Receiver User Details')
                        ->label('Reciver Details')
                        ->columns(1)
                        ->schema(
                            [
                                Select::make('authorized_user_id')
                                    ->label("Reciver")
                                    ->options(function (callable $get) {
                                        $senderId = $get('user_id');
                                        return User::whereNot('id', $senderId)->pluck('user_name', 'id');
                                    })
                            ],
                        )
                        ->visible(self::visibleForUser(field: 'authorized_user_type')),
                    Step::make('Receiver Guest Details')
                        ->label('Reciver Details')
                        ->columns(2)
                        ->schema(
                            [
                                TextInput::make('guest_first_name')
                                    ->label('First Name')
                                    ->maxLength(255)
                                    ->required(),
                                TextInput::make('guest_last_name')
                                    ->label('Last Name')
                                    ->maxLength(255)
                                    ->required(),
                                PhoneNumber::make('guest_phone', 'Phone'),
                                TextInput::make('guest_address')
                                    ->label('Address')
                                    ->required(),
                                LocationSelect::make('guest_city_id', 'guest_country_id', 'City', 'Country'),
                                NationalNumber::make('guest_national_number', 'National Number'),
                                DatePicker::make('birthday')
                                    ->label('Birthday')
                                    ->native(false),

                            ],
                        )
                        ->visible(self::visibleForGuest(field: 'authorized_user_type')),
                    Step::make('Delegation Data')
                        ->schema([
                            Grid::make()
                                ->schema([
                                    Select::make('parcel_id')
                                        ->required()
                                        ->options(function (callable $get) {
                                            $senderId = $get('user_id');
                                            return  self::getParcelAuthenticatedUser($senderId);
                                        }),
                                ]),
                            Grid::make(2)
                                ->schema([
                                    TextInput::make('authorized_code')
                                        ->disabled(),
                                    Select::make('authorized_status')
                                        ->label('Authorized Status')
                                        ->options(AuthorizationStatus::class)
                                        ->default(AuthorizationStatus::PENDING->value)
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
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('parcel.reciver_name')
                    ->sortable(),
                TextColumn::make('authorizedUser.name')
                    ->sortable(),
                TextColumn::make('authorized_user_type')
                    ->label('Authorized Type')
                    ->badge()
                    ->color(
                        fn($state) =>
                        $state === SenderType::GUEST_USER->value ? 'success' : 'danger'
                    ),
                TextColumn::make('authorized_code')
                    ->searchable(),
                TextColumn::make('authorized_status')
                    ->sortable()
                    ->badge()
                    ->color(
                        fn($state) =>
                        match ($state) {
                            AuthorizationStatus::PENDING->value => 'primary',
                            AuthorizationStatus::ACTIVE->value => 'secondary',
                            AuthorizationStatus::EXPIRED->value => 'danger',
                            AuthorizationStatus::USED->value => 'success',
                            AuthorizationStatus::CANCELLED->value => 'danger',
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
                ConfirmAuthReceiptAction::make(),
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
            'index' => Pages\ListParcelAuthorizations::route('/'),
            'create' => Pages\CreateParcelAuthorization::route('/create'),
            'edit' => Pages\EditParcelAuthorization::route('/{record}/edit'),
        ];
    }
    private static function getParcelAuthenticatedUser($senderId)
    {
        return Parcel::select('id', 'sender_id', 'sender_type', 'route_id', 'reciver_name')
            ->where('sender_id', $senderId)
            ->where('sender_type', SenderType::AUTHENTICATED_USER->value)
            ->get()
            ->mapWithKeys(
                function ($parcel) {
                    return [
                        $parcel->id => 'reciver name: '
                            . $parcel->reciver_name
                            . ' , route : '
                            . $parcel->routeLabel
                    ];
                }
            );
    }
}
