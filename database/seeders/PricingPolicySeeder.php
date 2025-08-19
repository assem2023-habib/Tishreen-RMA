<?php

namespace Database\Seeders;

use App\Enums\CurrencyType;
use App\Enums\PolicyTypes;
use App\Enums\PriceUnit;
use App\Models\PricingPolicy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PricingPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PricingPolicy::insert([
            [
                'policy_type' => PolicyTypes::WEIGHT->value,
                'price' => 10.00,
                'price_unit' => PriceUnit::KG->value,
                'limit_min' => 0,
                'limit_max' => 5,
                'currency' => CurrencyType::USA->value,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'policy_type' => PolicyTypes::WEIGHT->value,
                'price' => 18.00,
                'price_unit' => PriceUnit::KG->value,
                'limit_min' => 6,
                'limit_max' => 10,
                'currency' => CurrencyType::USA->value,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'policy_type' => PolicyTypes::WEIGHT->value,
                'price' => 25.00,
                'price_unit' => PriceUnit::KG->value,
                'limit_min' => 11,
                'limit_max' => 20,
                'currency' => CurrencyType::USA->value,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'policy_type' => PolicyTypes::FLAT->value,
                'price' => 40.00,
                'price_unit' => PriceUnit::PER_PARCEL->value,
                'limit_min' => 21,
                'limit_max' => 50,
                'currency' => CurrencyType::USA->value,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
