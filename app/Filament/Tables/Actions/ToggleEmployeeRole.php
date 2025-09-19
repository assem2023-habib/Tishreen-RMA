<?php

namespace App\Filament\Tables\Actions;

use App\Enums\RoleName;
use App\Models\Employee;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class ToggleEmployeeRole extends Action
{
    protected function setup(): void
    {
        parent::setUp();
        $this->name('toggleEmployeeRole');
        $this->label(fn(Employee $record) => $record->user->hasRole(RoleName::EMPLOYEE->value)
            ? 'Remove Employee Role'
            : 'Assign Employee Role');
        $this->icon(fn(Employee $record) => $record->user->hasRole(RoleName::EMPLOYEE->value)
            ? 'heroicon-o-x-circle'
            : 'heroicon-o-check-circle');
        $this->color(fn(Employee $record) => $record->user->hasRole(RoleName::EMPLOYEE->value)
            ? 'danger'
            : 'success');
        $this->requiresConfirmation();
        $this->modalHeading('change Role: Employee');
        $this->modalDescription('Are you sure you want to change the Employee role status?');
        $this->modalSubmitActionLabel('yes, change that');
        $this->action(function (Employee $record): void {
            // if (!Auth::user()->can('Make Employee') && !Auth::user()->hasRole(RoleName::SUPER_ADMIN->value)) {
            if (!Auth::user()->hasRole(RoleName::SUPER_ADMIN->value)) {
                $this->failure();
                $this->failureNotificationTitle('you dont have permission for that procedure.');
                return;
            }
            $employeeRole = Role::firstOrCreate([
                'name' => RoleName::EMPLOYEE->value,
            ]);
            if ($record->user->hasRole(RoleName::EMPLOYEE->value)) {
                $record->user->removeRole($employeeRole);
                $this->successNotificationTitle('Employee Role remove successfully');
            } else {
                $record->user->assignRole($employeeRole);
                $this->successNotificationTitle('Employee Role Assignment successfully');
            }
        });
        // $this->hidden(fn(): bool => !Auth::user()->can('Make Employee') && !Auth::user()->hasRole('Admin'));
        $this->hidden(fn(): bool => !Auth::user()->hasRole(RoleName::SUPER_ADMIN->value));
    }
    public static function getDefaultName(): ?string
    {
        return 'toggleEmployeeRole';
    }
}
