<?php

namespace App\Filament\Resources;

use App\Enums\GuestType;
use App\Enums\SenderType;
use App\Filament\Forms\Components\PhoneNumber;
use App\Filament\Resources\GuestUserResource\Pages;
use App\Filament\Resources\GuestUserResource\RelationManagers;
use App\Models\City;
use App\Models\GuestUser;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                Select::make('city_id')
                    ->label('City')
                    ->options(function () {
                        return City::select('id', 'en_name')
                            ->get()
                            ->mapWithKeys(function ($city) {
                                return [$city->id => $city->en_name];
                            });
                    }),
                TextInput::make('national_number')
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
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Name')
                    ->formatStateUsing(fn(GuestUser $record) => $record->first_name . ' ' . $record->last_name)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable(),

                Tables\Columns\TextColumn::make('national_number')
                    ->label('National No.')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('city.en_name')
                    ->label('City')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('address')
                    ->label('Address')
                    ->limit(25)
                    ->tooltip(fn($record) => $record->address),

                Tables\Columns\TextColumn::make('user_type')
                    ->label('Sender Type')
                    ->getStateUsing(fn($record) => $record->user_type) // القيم المخزنة في قاعدة البيانات
                    ->badge() // يعطي شكل Badge ملون
                    ->color(fn($state) => match ($state) {
                        GuestType::AUTHORIZED->value => 'success', // أخضر
                        GuestType::SENDER->value => 'primary', // أزرق
                        default => 'secondary', // رمادي لأي قيمة أخرى
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('birthday')
                    ->label('Birthday')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
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
            'index' => Pages\ListGuestUsers::route('/'),
            'create' => Pages\CreateGuestUser::route('/create'),
            'edit' => Pages\EditGuestUser::route('/{record}/edit'),
        ];
    }
}
