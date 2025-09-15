<?php

namespace App\Filament\Pages\Auth;

use App\Models\Country;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register as BaseRegister;
use App\Models\User;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Illuminate\Support\Str;
use League\Csv\Serializer\CastToArray;

class Register extends BaseRegister
{


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    // first name 
                    TextInput::make('first_name')
                        ->label('First Name')
                        ->helperText('Arabic letters only')
                        ->rules([
                            'required',
                            'regex:/^[A-Za-z\s]+$/',
                            'min:2',
                            'max:50',
                        ])
                        ->validationMessages([
                            'required' => 'Last name is required',
                            'regex' => 'Only English letters are allowed',
                            'min' => 'At least 2 characters required',
                            'max' => 'Maximum 50 characters allowed',
                        ]),
                    // last name 
                    TextInput::make('last_name')
                        ->label('Last Name')
                        ->helperText('English letters only')
                        ->rules([
                            'required',
                            'regex:/^[A-Za-z\s]+$/',
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
                        // email
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->rules(['required', 'email', 'unique:users,email'])
                            ->helperText('Valid email address required')
                            ->validationMessages([
                                'required' => 'Email is required',
                                'email' => 'Please enter a valid email',
                                'unique' => 'This email is already registered',
                            ])
                            ->columnSpan(2),
                        Grid::make(2)->schema([
                            // password
                            TextInput::make('password')
                                ->label('Password')
                                ->password()
                                ->revealable()
                                ->helperText('At least 8 characters, English letters and symbols allowed')
                                ->rules([
                                    'required',
                                    'regex:/^[A-Za-z0-9@#$%^&*!]+$/',
                                    'min:8',
                                    'confirmed',
                                ])
                                ->validationMessages([
                                    'required' => 'Password is required',
                                    'regex' => 'Only English letters, numbers, and symbols are allowed',
                                    'min' => 'Password must be at least 8 characters',
                                    'confirmed' => 'Password and confirmation password should be same'
                                ]),
                            // confirmation password
                            TextInput::make('password_confirmation')
                                ->label('Password Confirmation')
                                ->password()
                                ->revealable()
                                ->helperText('At least 8 characters, English letters and symbols allowed')
                                ->rules([
                                    'required',
                                    'regex:/^[A-Za-z0-9@#$%^&*!]+$/',
                                    'min:8',

                                ])
                                ->validationMessages([
                                    'required' => 'Password is required',
                                    'regex' => 'Only English letters, numbers, and symbols are allowed',
                                    'min' => 'Password must be at least 8 characters',

                                ])
                        ]),
                    ]),

                Grid::make(2)->schema([
                    // phone
                    PhoneInput::make('phone')->autoPlaceholder('aggressive'),
                    //birthady
                    DatePicker::make('birthday')
                        ->label('Date of Birth')
                        ->native(false)
                        ->required()
                        ->validationMessages([
                            'required' => 'Date of birth is required',
                        ]),
                ]),

                // Country and City
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
                            ->options(
                                function (callable $get) {

                                    $country = Country::find($get('country_id'));

                                    return $country ? $country
                                        ->cities()
                                        ->pluck('en_name', 'id') : [];
                                }
                            )

                            ->validationMessages([
                                'required' => 'Please select a city',
                            ]),

                    ]),
                // National Number
                TextInput::make('national_number')
                    ->label('National Number')
                    ->helperText('Exactly 11 digits')
                    ->rules([
                        'required',
                        'digits:11',
                        'regex:/^[0-9]+$/',
                        'unique:users,national_number',
                    ])
                    ->validationMessages([
                        'required' => 'National number is required',
                        'digits' => 'Must be exactly 11 digits',
                        'regex' => 'Only digits are allowed',
                        'unique' => 'This national number is already used',
                    ]),


            ]);

        // $data = $this->form->getState();
        // dd($data->toArray());
    }

    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $base = Str::before($data['email'], '@');
        $username = $base;
        $count = 1;

        while (User::where('user_name', $username)->exists()) {
            $username = $base . $count++;
        }

        $data['user_name'] = $username;

        return $data;
    }
}
