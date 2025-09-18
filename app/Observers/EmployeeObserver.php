<?php

namespace App\Observers;

use App\Enums\RoleName;
use App\Models\Employee;

class EmployeeObserver
{
    /**
     * Handle the Employee "created" event.
     */
    public function creating(Employee $employee): void
    {
        $employee->user->assignRole(RoleName::EMPLOYEE->value);
    }
    public function created(Employee $employee): void
    {
        //
    }

    /**
     * Handle the Employee "updated" event.
     */
    public function updated(Employee $employee): void
    {
        //
    }

    public function deleting(Employee $employee): void
    {
        $employee->user->removeRole(RoleName::EMPLOYEE->value);
    }
    /**
     * Handle the Employee "deleted" event.
     */
    public function deleted(Employee $employee): void
    {
        // $employee->user->removeRole(RoleName::EMPLOYEE->value);
    }

    /**
     * Handle the Employee "restored" event.
     */
    public function restored(Employee $employee): void
    {
        //
    }

    /**
     * Handle the Employee "force deleted" event.
     */
    public function forceDeleted(Employee $employee): void
    {
        //
    }
}
