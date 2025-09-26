<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(
            [
                CountrySeeder::class,
                CitySeeder::class,
                UserSeeder::class,
                BranchSeeder::class,
                UsagePoliciesSeeder::class,
                FrequentlyAskedQuestionsSeeder::class,
                PricingPolicySeeder::class,
                BranchRouteSeeder::class,
                ParcelSeeder::class,
                RateSeeder::class,
                RoleSeeder::class,
                PermissionSeeder::class,
                RolePermissionSeeder::class,
                UserRoleSeeder::class,
                EmployeeSeeder::class,
                TruckSeeder::class,
                BranchRouteDaySeeder::class,
                TruckBranchRouteDaySeeder::class,
            ]
        );
    }
}
