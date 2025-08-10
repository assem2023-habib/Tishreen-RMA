<?php

namespace Database\Seeders;

use App\Enums\RatingForType;
use App\Models\Rate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //         user_id
        // rateable_id
        // rating
        // comment
        // rateable_type
        $rates = [
            [
                'user_id' => 2,
                'rateable_id' => null,
                'rating' => 3,
                'comment' =>  'comment comment',
                'rateable_type' => RatingForType::APPLICATION,
            ],
            [
                'user_id' => 2,
                'rateable_id' => 1,
                'rating' => 1,
                'comment' => 'comment comment comment',
                'rateable_type' => RatingForType::EMPLOYEE
            ]
        ];
        foreach ($rates as $rate) {
            Rate::create($rate);
        }
    }
}
