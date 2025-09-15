<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'en_name' => 'syria',
                'ar_name' => 'سوريا',
                'code' => 'SY',
                'domain_code' => '+963',
            ]
        ];
        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
