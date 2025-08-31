<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BranchRoute;

class BranchRouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $routes = [
            [
                'from_branch_id' => 1,
                'to_branch_id' => 2,
                'day' => 'Sunday',
                'is_active' => 1,
                'estimated_departur_time' => '08:00:00',
                'estimated_arrival_time' => '12:00:00',
                'distance_per_kilo' => 350.5,
            ],
            [
                'from_branch_id' => 2,
                'to_branch_id' => 3,
                'day' => 'Monday',
                'is_active' => 1,
                'estimated_departur_time' => '09:00:00',
                'estimated_arrival_time' => '13:30:00',
                'distance_per_kilo' => 210.7,
            ],
            [
                'from_branch_id' => 1,
                'to_branch_id' => 4,
                'day' => 'Tuesday',
                'is_active' => 1,
                'estimated_departur_time' => '07:30:00',
                'estimated_arrival_time' => '11:45:00',
                'distance_per_kilo' => 290.3,
            ],
            [
                'from_branch_id' => 3,
                'to_branch_id' => 5,
                'day' => 'Wednesday',
                'is_active' => 1,
                'estimated_departur_time' => '10:15:00',
                'estimated_arrival_time' => '14:30:00',
                'distance_per_kilo' => 180.4,
            ],
        ];

        foreach ($routes as $route) {
            BranchRoute::create($route);
        }
    }
}
