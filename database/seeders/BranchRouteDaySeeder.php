<?php

namespace Database\Seeders;

use App\Enums\DaysOfWeek;
use App\Models\BranchRouteDays;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchRouteDaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $routesDays = [
            // Damascus → Aleppo
            [
                'day_of_week' => DaysOfWeek::SUNDAY->value,
                'branch_route_id' => 1,
                'estimated_departur_time' => '08:00:00',
                'estimated_arrival_time' => '12:30:00',
            ],
            [
                'day_of_week' => DaysOfWeek::WEDNESDAY->value,
                'branch_route_id' => 1,
                'estimated_departur_time' => '09:00:00',
                'estimated_arrival_time' => '13:15:00',
            ],

            // Damascus → Homs
            [
                'day_of_week' => DaysOfWeek::MONDAY->value,
                'branch_route_id' => 2,
                'estimated_departur_time' => '07:30:00',
                'estimated_arrival_time' => '09:00:00',
            ],
            [
                'day_of_week' => DaysOfWeek::THURSDAY->value,
                'branch_route_id' => 2,
                'estimated_departur_time' => '14:00:00',
                'estimated_arrival_time' => '15:30:00',
            ],

            // Homs → Latakia
            [
                'day_of_week' => DaysOfWeek::TUESDAY->value,
                'branch_route_id' => 3,
                'estimated_departur_time' => '10:00:00',
                'estimated_arrival_time' => '13:00:00',
            ],
            [
                'day_of_week' => DaysOfWeek::FRIDAY->value,
                'branch_route_id' => 3,
                'estimated_departur_time' => '15:00:00',
                'estimated_arrival_time' => '18:30:00',
            ],
        ];
        foreach ($routesDays as $routeDay) {
            BranchRouteDays::create($routeDay);
        }
    }
}
