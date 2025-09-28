<?php

namespace App\Filament\Resources;

use App\Enums\GuestType;
use App\Filament\Forms\Components\{LocationSelect, PhoneNumber, NationalNumber};
use App\Filament\Helpers\TableActions;
use App\Filament\Resources\GuestUserResource\Pages;
use App\Filament\Tables\Columns\Timestamps;
use App\Models\GuestUser;
use Filament\Forms\Components\{DatePicker, TextInput, Select};
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GuestUserResource extends Resource
{
    protected static ?string $model = GuestUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationGroup = 'Users';

    protected static ?int $navigationSort = 2;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('first_name')
                    ->label('First Name')
                    ->required(),
                TextInput::make('last_name')
                    ->label('Last Name')
                    ->required(),
                PhoneNumber::make('phone', 'Phone'),
                TextInput::make('address')
                    ->label('Address')
                    ->required(),
                LocationSelect::make('city_id', 'country_id', 'City', 'Country'),
                // Select::make('city_id')
                //     ->label('City')
                //     ->options(function () {
                //         return City::select('id', 'en_name')
                //             ->get()
                //             ->mapWithKeys(function ($city) {
                //                 return [$city->id => $city->en_name];
                //             });
                //     }),
                NationalNumber::make('national_number', 'National Number'),
                DatePicker::make('birthday')
                    ->label('Birthday')
                    ->native(false),
                Select::make('user_type')
                    ->label('Sender Type')
                    ->options(GuestType::class)
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->badge()
                    ->searchable(),

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

                TextColumn::make('user_type')
                    ->label('Sender Type')
                    ->getStateUsing(fn($record) => $record->user_type) // القيم المخزنة في قاعدة البيانات
                    ->badge() // يعطي شكل Badge ملون
                    ->color(fn($state) => match ($state) {
                        GuestType::AUTHORIZED->value => 'success', // أخضر
                        GuestType::SENDER->value => 'primary', // أزرق
                        default => 'secondary', // رمادي لأي قيمة أخرى
                    })
                    ->sortable(),

                TextColumn::make('birthday')
                    ->label('Birthday')
                    ->date()
                    ->sortable(),
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
            'index' => Pages\ListGuestUsers::route('/'),
            'create' => Pages\CreateGuestUser::route('/create'),
            'edit' => Pages\EditGuestUser::route('/{record}/edit'),
        ];
    }
}
