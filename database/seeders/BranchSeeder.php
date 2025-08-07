<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            [
                'branch_name' => 'فرع دمشق المركزي',
                'city_id' => 1,
                'address' => 'شارع بغداد، دمشق',
                'phone' => '0111234567',
                'email' => 'damascus@company.com',
                'latitude' => 33.5138,
                'longitude' => 36.2765,
                'status' => 1,
            ],
            [
                'branch_name' => 'فرع حلب الشمالي',
                'city_id' => 1,
                'address' => 'شارع القوتلي، حلب',
                'phone' => '0217654321',
                'email' => 'aleppo@company.com',
                'latitude' => 36.2021,
                'longitude' => 37.1343,
                'status' => 1,
            ],
            [
                'branch_name' => 'فرع حمص',
                'city_id' => 1,
                'address' => 'شارع خالد بن الوليد، حمص',
                'phone' => '0319876543',
                'email' => 'homs@company.com',
                'latitude' => 34.7308,
                'longitude' => 36.7090,
                'status' => 1,
            ],
            [
                'branch_name' => 'فرع اللاذقية',
                'city_id' => 1,
                'address' => 'شارع بيروت، اللاذقية',
                'phone' => '0411234567',
                'email' => 'latakia@company.com',
                'latitude' => 35.5406,
                'longitude' => 35.7760,
                'status' => 1,
            ],
            [
                'branch_name' => 'فرع حماة',
                'city_id' => 1,
                'address' => 'شارع عباس بن فرناس، حماة',
                'phone' => '0315555555',
                'email' => 'hama@company.com',
                'latitude' => 35.1318,
                'longitude' => 36.7578,
                'status' => 1,
            ],
            [
                'branch_name' => 'فرع درعا',
                'city_id' => 1,
                'address' => 'شارع الثورة، درعا',
                'phone' => '0261234567',
                'email' => 'daraa@company.com',
                'latitude' => 32.6188,
                'longitude' => 36.1013,
                'status' => 1,
            ],
            [
                'branch_name' => 'فرع القنيطرة',
                'city_id' => 1,
                'address' => 'شارع الوحدة، القنيطرة',
                'phone' => '0269876543',
                'email' => 'quneitra@company.com',
                'latitude' => 33.1367,
                'longitude' => 35.8277,
                'status' => 1,
            ],
            [
                'branch_name' => 'فرع الحسكة',
                'city_id' => 1,
                'address' => 'شارع الثورة، الحسكة',
                'phone' => '0527654321',
                'email' => 'alhasakah@company.com',
                'latitude' => 36.4956,
                'longitude' => 40.7462,
                'status' => 1,
            ],
            [
                'branch_name' => 'فرع السويداء',
                'city_id' => 1,
                'address' => 'شارع الثورة، السويداء',
                'phone' => '0316549872',
                'email' => 'asweida@company.com',
                'latitude' => 32.7096,
                'longitude' => 37.0731,
                'status' => 1,
            ],
            [
                'branch_name' => 'فرع إدلب',
                'city_id' => 1,
                'address' => 'شارع الثورة، إدلب',
                'phone' => '0271239876',
                'email' => 'idlib@company.com',
                'latitude' => 35.9306,
                'longitude' => 36.6314,
                'status' => 1,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
