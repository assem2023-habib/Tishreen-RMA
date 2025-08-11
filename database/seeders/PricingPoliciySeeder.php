<?php

namespace Database\Seeders;

use App\Models\PricingPoliciy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PricingPoliciySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PricingPoliciy::insert([
            [
                'price' => 10.00,
                'price_unit' => 'per kg',
                'weight_limit_min' => 0,
                'weight_limit_max' => 5,
                'currency' => 'USD',
            ],
            [
                'price' => 18.00,
                'price_unit' => 'per kg',
                'weight_limit_min' => 6,
                'weight_limit_max' => 10,
                'currency' => 'USD',
            ],
            [
                'price' => 25.00,
                'price_unit' => 'per kg',
                'weight_limit_min' => 11,
                'weight_limit_max' => 20,
                'currency' => 'USD',
            ],
            [
                'price' => 40.00,
                'price_unit' => 'flat',
                'weight_limit_min' => 21,
                'weight_limit_max' => 50,
                'currency' => 'USD',
            ],
        ]);
    }
}
