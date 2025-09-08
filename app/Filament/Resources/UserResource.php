<?php

namespace App\Filament\Resources;

use App\Enums\UserAccountStatus;
use App\Filament\Resources\UserResource\Pages;
use App\Models\Country;
use App\Models\User;
use App\Models\UserRestriction;
use Filament\Forms\Components\{TextInput, Grid, Select, DatePicker, Toggle};
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Users';

    protected static ?int $navigationSort = 1;

    protected static bool $shouldRegisterNavigation = true;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        TextInput::make('first_name')
                            ->label('First Name')
                            // ->helperText('Arabic letters only')
                            ->rules([
                                'required',
                                // 'regex:/^[A-Za-z\s]+$/',
                                'min:2',
                                'max:50',
                            ])
                            ->validationMessages([
                                'required' => 'Last name is required',
                                // 'regex' => 'Only English letters are allowed',
                                'min' => 'At least 2 characters required',
                                'max' => 'Maximum 50 characters allowed',
                            ]),

                        TextInput::make('last_name')
                            ->label('Last Name')
                            // ->helperText('English letters only')
                            ->rules([
                                'required',
                                // 'regex:/^[A-Za-z\s]+$/',
                                'min:2',
                                'max:50',
                            ])
                            ->validationMessages([
                                'required' => 'Last name is required',
                                'regex' => 'Only English letters are allowed',
                                'min' => 'At least 2 characters required',
                                'max' => 'Maximum 50 characters allowed',
                            ]),
                    ]),

                Grid::make(2)
                    ->schema([

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->helperText('Valid email address required')
                            ->validationMessages([
                                'required' => 'Email is required',
                                'email' => 'Please enter a valid email',
                                'unique' => 'This email is already registered',
                            ]),
                        // Select::make('account_status')
                        //     ->label('Account Status')
                        //     ->options(UserAccountStatus::class)
                        //     ->enum(UserAccountStatus::class)
                        //     ->default(null)
                        //     ->required(), // delete the column for restrication users
                    ]),

                Grid::make(2)
                    ->schema([
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->helperText('At least 8 characters, English letters and symbols allowed')
                            ->rules([
                                'regex:/^[A-Za-z0-9@#$%^&*!]+$/',
                                'min:8',
                            ])
                            ->required(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                            ->validationMessages([
                                'required' => 'Password is required',
                                'regex' => 'Only English letters, numbers, and symbols are allowed',
                                'min' => 'Password must be at least 8 characters',
                            ])
                            ->dehydrated(fn($state) => filled($state)),

                        TextInput::make('national_number')
                            ->label('National Number')
                            ->helperText('Exactly 11 digits')
                            ->unique(ignoreRecord: true)
                            ->rules([
                                'required',
                                'digits:11',
                                'regex:/^[0-9]+$/',
                            ])
                            ->validationMessages([
                                'required' => 'National number is required',
                                'digits' => 'Must be exactly 11 digits',
                                'regex' => 'Only digits are allowed',
                                'unique' => 'This national number is already used',
                            ]),
                    ]),

                Grid::make(2)
                    ->schema([
                        Select::make('country_id')
                            ->label('Country')
                            ->options(Country::all()->pluck('en_name', 'id'))
                            ->live()
                            ->afterStateUpdated(fn(callable $set) => $set('city_id', null))

                            ->validationMessages([
                                'required' => 'Please select a country',
                            ]),

                        Select::make('city_id')
                            ->label('City')
                            ->options(function (callable $get) {
                                $country = Country::find($get('country_id'));
                                return $country ? $country->cities()->pluck('en_name', 'id') : [];
                            })

                            ->validationMessages([
                                'required' => 'Please select a city',
                            ]),
                    ]),

                Grid::make(2)
                    ->schema([
                        TextInput::make('address')
                            ->label('Address')
                            ->maxLength(255)
                            ->helperText('Full address, up to 255 characters')
                            ->validationMessages([
                                'required' => 'Address is required',
                                'max' => 'Maximum 255 characters allowed',
                            ]),

                        PhoneInput::make('phone')
                            ->autoPlaceholder('aggressive')
                            ->helperText('Include country code, e.g. +9639XXXXXXX')
                            ->rules(['required', 'regex:/^(\+?\d{6,15})$/'])
                            ->validationMessages([
                                'required' => 'Phone number is required',
                                'regex' => 'Invalid phone number format',
                            ]),

                    ]),

                DatePicker::make('birthday')
                    ->label('Date of Birth')
                    ->native(false)
                    ->required()
                    ->validationMessages([
                        'required' => 'Date of birth is required',
                    ]),
                Toggle::make('is_verified')
                    ->label('email Verified ? '),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')
                    ->label('Name')
                    ->formatStateUsing(function (User $record) {
                        return $record->first_name . ' ' . $record->last_name;
                    }),

                TextColumn::make('user_name')
                    ->label('Username')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('national_number')
                    ->label('National No.')
                    ->searchable()
                    ->sortable(),


                TextColumn::make('city.en_name')
                    ->label('City')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('address')
                    ->label('Address')
                    ->limit(25)
                    ->tooltip(fn($record) => $record->address),

                TextColumn::make('phone')
                    ->label('Phone'),
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
            'index' => Pages\ListUser::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
