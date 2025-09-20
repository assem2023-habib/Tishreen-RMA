<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employee = Employee::create(
            [
                'user_id' => '4',
                'branch_id' => '1',
                'beging_date' => now(),
                'is_active' => 1,
            ]
        );
        $employee->user->assignRole(RoleName::EMPLOYEE->value);

        $employee = Employee::create(
            [
                'user_id' => '5',
                'branch_id' => '2',
                'beging_date' => now(),
                'is_active' => 1,
            ]
        );
        $employee->user->assignRole(RoleName::EMPLOYEE->value);

        $employee = Employee::create(
            [
                'user_id' => '6',
                'branch_id' => '3',
                'beging_date' => now(),
                'is_active' => 1,
            ]
        );
        $employee->user->assignRole(RoleName::EMPLOYEE->value);
    }
}
