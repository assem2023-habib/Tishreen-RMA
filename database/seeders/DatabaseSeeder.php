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
                RolesAndPermissionsSeeder::class,
                BranchSeeder::class,
                RateSeeder::class,
                UsagePoliciesSeeder::class,
                FrequentlyAskedQuestionsSeeder::class,
                PricingPolicySeeder::class,
                BranchRouteSeeder::class,
            ]
        );
    }
}
