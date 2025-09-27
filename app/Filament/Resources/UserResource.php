<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\{ActiveToggle, PhoneNumber, NationalNumber};
use App\Filament\Helpers\TableActions;
use App\Filament\Resources\UserResource\Pages;
use App\Models\{Country, User};
use Filament\Forms\Components\{TextInput, Grid, Select, DatePicker, FileUpload};
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\{TextColumn, ImageColumn};
use Filament\Tables\Table;

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
                            ->rules([
                                'required',
                                'min:2',
                                'max:50',
                            ])
                            ->validationMessages([
                                'required' => 'Last name is required',
                                'min' => 'At least 2 characters required',
                                'max' => 'Maximum 50 characters allowed',
                            ]),

                        TextInput::make('last_name')
                            ->label('Last Name')
                            ->rules([
                                'required',
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
                    ]),

                Grid::make(2)
                    ->schema([]),

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
                        PhoneNumber::make('phone', 'User Phone'),
                    ]),

                NationalNumber::make('national_number', 'National Number'),
                DatePicker::make('birthday')
                    ->label('Date of Birth')
                    ->native(false)
                    ->required()
                    ->validationMessages([
                        'required' => 'Date of birth is required',
                    ]),
                ActiveToggle::make('is_verified', 'email Verified ? '),
                FileUpload::make('image_profile')
                    ->disk('public')
                    ->image()
                    ->imageEditor()
                    ->imageEditorViewportWidth('1920')
                    ->imageEditorViewportHeight('1080')
                    ->imageCropAspectRatio('1:1')
                    ->imageResizeTargetWidth(300)
                    ->imageResizeTargetHeight(300)
                    ->helperText('Please make user the image size is 300*300')
                    ->directory('flags')
                    ->label('Image'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name'),

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
                ImageColumn::make('image_profile')
                    ->disk('public')->label('image'),
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
