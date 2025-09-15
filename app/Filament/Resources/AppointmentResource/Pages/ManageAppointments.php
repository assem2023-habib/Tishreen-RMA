<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use App\Filament\Resources\AppointmentResource;
use Filament\Resources\Pages\ManageRecords;
use App\Models\Branch;
use App\Services\AppointmentHelper;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;

class ManageAppointments extends ManageRecords
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generateAppointments')
                ->label('Add Appointments')
                ->icon('heroicon-o-calendar-days')
                ->form([
                    Forms\Components\Select::make('branch_id')
                        ->label('Select Branch')
                        ->options(Branch::pluck('branch_name', 'id'))
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data) {
                    // Call the algorithm to generate appointments
                    AppointmentHelper::generateWeeklyAppointments($data['branch_id']);
                    Notification::make()
                        ->success()
                        ->title('Appointments Created')
                        ->body('Weekly appointments for the selected branch have been generated successfully.')
                        ->send();
                }),
        ];
    }
}
