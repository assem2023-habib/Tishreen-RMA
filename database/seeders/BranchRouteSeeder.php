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
                'is_active' => 1,
                'distance_per_kilo' => 350.5,
            ],
            [
                'from_branch_id' => 2,
                'to_branch_id' => 3,
                'is_active' => 1,
                'distance_per_kilo' => 210.7,
            ],
            [
                'from_branch_id' => 1,
                'to_branch_id' => 4,
                'is_active' => 1,
                'distance_per_kilo' => 290.3,
            ],
            [
                'from_branch_id' => 3,
                'to_branch_id' => 5,
                'is_active' => 1,
                'distance_per_kilo' => 180.4,
            ],
        ];

        foreach ($routes as $route) {
            BranchRoute::create($route);
        }
    }
}
