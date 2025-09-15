<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::select('id', 'email')
            ->where('email', 'admin@gmail.com')
            ->first();
        $admin->assignRole(RoleName::SUPER_ADMIN->value);
    }
}
