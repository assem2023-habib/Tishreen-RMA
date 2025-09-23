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
                'limit_max' => 5.99,
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
                'limit_max' => 10.99,
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
                'limit_max' => 20.99,
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
                'limit_max' => 50.99,
                'currency' => CurrencyType::USA->value,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        PricingPolicy::insert([
            [
                'policy_type' => PolicyTypes::WEIGHT->value,
                'price' => 50.00, // مثال: 5000 ليرة للكيلو حتى 5 كيلو
                'price_unit' => PriceUnit::KG->value,
                'limit_min' => 0,
                'limit_max' => 5.99,
                'currency' => CurrencyType::SYRIA->value,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'policy_type' => PolicyTypes::WEIGHT->value,
                'price' => 90.00, // من 6 إلى 10 كيلو
                'price_unit' => PriceUnit::KG->value,
                'limit_min' => 6,
                'limit_max' => 10.99,
                'currency' => CurrencyType::SYRIA->value,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'policy_type' => PolicyTypes::WEIGHT->value,
                'price' => 125.00, // من 11 إلى 20 كيلو
                'price_unit' => PriceUnit::KG->value,
                'limit_min' => 11,
                'limit_max' => 20.99,
                'currency' => CurrencyType::SYRIA->value,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'policy_type' => PolicyTypes::WEIGHT->value,
                'price' => 150.00, // من 11 إلى 20 كيلو
                'price_unit' => PriceUnit::KG->value,
                'limit_min' => 21.00,
                'limit_max' => 50.99,
                'currency' => CurrencyType::SYRIA->value,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'policy_type' => PolicyTypes::WEIGHT->value,
                'price' => 150.00, // من 11 إلى 20 كيلو
                'price_unit' => PriceUnit::KG->value,
                'limit_min' => 51.00,
                'limit_max' => 75.99,
                'currency' => CurrencyType::SYRIA->value,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'policy_type' => PolicyTypes::FLAT->value,
                'price' => 200.00, // طرد 21 إلى 50 كيلو
                'price_unit' => PriceUnit::PER_PARCEL->value,
                'limit_min' => 21,
                'limit_max' => 50.99,
                'currency' => CurrencyType::SYRIA->value,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
