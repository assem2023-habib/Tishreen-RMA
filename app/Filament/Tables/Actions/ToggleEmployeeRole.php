<?php

namespace App\Filament\Tables\Actions;

use App\Models\Role;
use App\Models\User;
use Filament\Tables\Actions\Action;

class ToggleEmployeeRole extends Action
{
    protected function setup(): void
    {
        parent::setUp();
        $this->name('toggleEmployeeRole');
        $this->label(fn(User $record) => $record->hasRole('Employee')
            ? 'Remove Employee Role'
            : 'Assign Employee Role');
        $this->icon(fn(User $record) => $record->hasRole('Employee')
            ? 'heroicon-o-x-circle'
            : 'heroicon-o-check-circle');
        $this->color(fn(User $record) => $record->hasRole('Employee')
            ? 'danger'
            : 'success');
        $this->requiresConfirmation();
        $this->modalHeading('change role employee');
        $this->modalSubheading('are you shure to exchange the status employee role');
        $this->modalButton('yes, change that');
        $this->action(function (User $record): void {
            if (!auth()->user()->can('Make Employee') && !auth()->user()->hasRole('Admin')) {
                $this->failure();
                $this->failureNotificationTitle('you dont have permission for that procedure.');
                return;
            }
            $employeeRole = Role::firstOrCreate([
                'name' => 'Employee',
            ]);
            if ($record->hasRole('Employee')) {
                $record->removeRole($employeeRole);
                $this->successNotificationTitle('Employee Role remove successfully');
            } else {
                $record->assignRole($employeeRole);
                $this->successNotificationTitle('Employee Role Assignment successfully');
            }
        });
        $this->hidden(fn(): bool => !auth()->user()->can('Make Employee') && !auth()->user()->hasRole('Admin'));
    }
    public static function getDefaultName(): ?string
    {
        return 'toggleEmployeeRole';
    }
}
