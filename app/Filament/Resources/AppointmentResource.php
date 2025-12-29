<?php

namespace App\Filament\Resources;

use App\Enums\AppointmentStatus;
use App\Filament\Resources\AppointmentResource\Pages;
use App\Models\Appointment;
use Filament\Forms;
use App\Support\SharedNotification as Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use App\Models\User;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Pickup Appointments';
    protected static ?string $navigationLabel = 'Center Entry Appointments';
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
                Forms\Components\Select::make('status')
                    ->options(AppointmentStatus::class)
                    ->required(),
                Forms\Components\DateTimePicker::make('booked_at')
                    ->label('Booked At')
                    ->disabled(),
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
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('booked_at')
                    ->dateTime()
                    ->label('Booked At')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(AppointmentStatus::class),
            ])
            ->actions([
                Tables\Actions\Action::make('cancel')
                    ->label('Cancel Appointment')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn(Appointment $record) => in_array($record->status, [AppointmentStatus::PENDING, AppointmentStatus::CONFIRMED]))
                    ->action(function (Appointment $record) {
                        $record->update([
                            'status' => AppointmentStatus::CANCELLED,
                            'booked' => false,
                        ]);

                        // Send Notification to User
                        if ($record->user instanceof User) {
                            Notification::make()
                                ->title('تم إلغاء الموعد')
                                ->body("تم إلغاء موعد المراجعة الخاص بك بتاريخ {$record->date} في الساعة {$record->time}.")
                                ->danger()
                                ->icon('heroicon-o-calendar-days')
                                ->sendToDatabase($record->user)
                                ->broadcast($record->user);
                        }

                        // Optional: Clear parcel link
                        \App\Models\Parcel::where('appointment_id', $record->id)->update(['appointment_id' => null]);
                    })
                    ->requiresConfirmation(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAppointments::route('/'),
        ];
    }
}
