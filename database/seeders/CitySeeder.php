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
                'en_name' => 'Aleppo',
                'ar_name' => 'حلب'
            ],
            [
                'country_id' => 1,
                'en_name' => 'Al-Hasakah',
                'ar_name' => 'الحسكة'
            ],
            [
                'country_id' => 1,
                'en_name' => 'Hama',
                'ar_name' => 'حماة'
            ],
            [
                'country_id' => 1,
                'en_name' => 'Homs',
                'ar_name' => 'حمص'
            ],
            [
                'country_id' => 1,
                'en_name' => 'Idlib',
                'ar_name' => 'إدلب'
            ],
            [
                'country_id' => 1,
                'en_name' => 'Latakia',
                'ar_name' => 'اللاذقية'
            ],
            [
                'country_id' => 1,
                'en_name' => 'Raqqa',
                'ar_name' => 'الرقة'
            ],
            [
                'country_id' => 1,
                'en_name' => 'Deir ez-Zor',
                'ar_name' => 'دير الزور'
            ],
            [
                'country_id' => 1,
                'en_name' => 'Damascus',
                'ar_name' => 'دمشق'
            ],
            [
                'country_id' => 1,
                'en_name' => 'Rif Dimashq',
                'ar_name' => 'ريف دمشق'
            ],
            [
                'country_id' => 1,
                'en_name' => 'Tartus',
                'ar_name' => 'طرطوس'
            ],
            [
                'country_id' => 1,
                'en_name' => 'Quneitra',
                'ar_name' => 'القنيطرة'
            ],
            [
                'country_id' => 1,
                'en_name' => 'Daraa',
                'ar_name' => 'درعا'
            ],
            [
                'country_id' => 1,
                'en_name' => 'Al-Suwayda',
                'ar_name' => 'السويداء'
            ],
        ];
        foreach ($cities as $city) {
            City::create($city);
        }
    }
}
