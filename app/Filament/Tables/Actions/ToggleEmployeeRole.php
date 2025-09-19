<?php

namespace App\Filament\Tables\Actions;

use App\Models\User;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

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
        $this->modalDescription('Are you sure you want to change the Employee role status?');
        $this->modalSubmitActionLabel('yes, change that');
        $this->action(function (User $record): void {
            if (!Auth::user()->can('Make Employee') && !Auth::user()->hasRole('Admin')) {
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
        $this->hidden(fn(): bool => !Auth::user()->can('Make Employee') && !Auth::user()->hasRole('Admin'));
    }
    public static function getDefaultName(): ?string
    {
        return 'toggleEmployeeRole';
    }
}
