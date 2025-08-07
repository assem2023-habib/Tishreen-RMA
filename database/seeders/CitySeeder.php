<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            [
                'country_id' => 1,
                'en_name' => 'latakia',
                'ar_name' => 'اللاذقية',
            ],
            [
                'country_id' => 1,
                'en_name' => 'tartous',
                'ar_name' => 'طرطوس',
            ]
        ];
        foreach ($cities as $city) {
            City::create($city);
        }
    }
}
