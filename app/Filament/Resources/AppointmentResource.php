<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Models\Appointment;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Appointments';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('branch_id')
                    ->relationship('branch', 'branch_name')
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->options(
                        fn() => \App\Models\User::all()
                            ->mapWithKeys(fn($u) => [$u->id => $u->first_name . ' ' . $u->last_name])
                            ->toArray()
                    )
                    ->searchable()
                    ->nullable(),
                Forms\Components\DatePicker::make('date')->required(),
                Forms\Components\TimePicker::make('time')->required(),
                Forms\Components\Toggle::make('booked')->label('Booked'),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('branch.branch_name')
                    ->label('Branch')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.first_name')
                    ->label('First Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.last_name')
                    ->label('Last Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->sortable(),
                Tables\Columns\TextColumn::make('time')
                    ->label('Time')
                    ->searchable(),
                Tables\Columns\IconColumn::make('booked')
                    ->boolean()
                    ->label('Booked'),
            ])
            ->filters([])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAppointments::route('/'),
        ];
    }
}
