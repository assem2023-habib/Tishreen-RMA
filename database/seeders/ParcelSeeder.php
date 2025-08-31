<?php

namespace Database\Seeders;

use App\Enums\SenderType;
use App\Models\Parcel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParcelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parcels = [
            [
                'sender_id' => 1,
                'sender_type' => SenderType::AUTHENTICATED_USER->value,
                'route_id' => 1,
                'reciver_name' => 'seed1',
                'reciver_address' => 'seed1',
                'reciver_phone' => '0987654321',
                'weight' => 22.4,
                'is_paid' => 1,
            ],
            [
                'sender_id' => 1,
                'sender_type' => SenderType::AUTHENTICATED_USER->value,
                'route_id' => 1,
                'reciver_name' => 'seed2',
                'reciver_address' => 'seed2',
                'reciver_phone' => '0987654321',
                'weight' => 22.4,
                'is_paid' => 1,
            ],
            [
                'sender_id' => 1,
                'sender_type' => SenderType::AUTHENTICATED_USER->value,
                'route_id' => 1,
                'reciver_name' => 'seed3',
                'reciver_address' => 'seed3',
                'reciver_phone' => '0987654321',
                'weight' => 22.4,
                'is_paid' => 1,
            ],
        ];
        foreach ($parcels as $parcel) {
            Parcel::create($parcel);
        }
    }
}
