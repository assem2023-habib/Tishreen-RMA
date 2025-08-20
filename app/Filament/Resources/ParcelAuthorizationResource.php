<?php

namespace App\Filament\Resources;

use App\Enums\SenderType;
use App\Filament\Resources\ParcelAuthorizationResource\Pages;
use App\Filament\Resources\ParcelAuthorizationResource\RelationManagers;
use App\Models\City;
use App\Models\GuestUser;
use App\Models\ParcelAuthorization;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
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
                                    ->reactive()
                                    ->required(),
                            ],
                        ),
                    Step::make('Sender Authenticated Details')
                        ->label('Sender Details')
                        ->columns(1)
                        ->schema(
                            [
                                Select::make('authorized_user_id')
                                    ->label("Reciver")
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
                    Step::make('')
                        ->schema([
                            TextInput::make('parcels')
                                ->required()
                                ->numeric(),
                            // TextInput::make('authorized_user_id')
                            //     ->required()
                            //     ->numeric(),
                            // TextInput::make('authorized_user_type')
                            //     ->required(),
                            TextInput::make('authorized_code')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('authorized_status')
                                ->required(),
                            DateTimePicker::make('generated_at')
                                ->required(),
                            DateTimePicker::make('expired_at'),
                            DateTimePicker::make('used_at'),
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
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('parcels')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('authorized_user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('authorized_user_type'),
                Tables\Columns\TextColumn::make('authorized_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('authorized_status'),
                Tables\Columns\TextColumn::make('generated_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expired_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('used_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cancellation_reason')
                    ->searchable(),
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
        return fn(callable $get) => $get('sender_type') === GuestUser::class;
    }
}
