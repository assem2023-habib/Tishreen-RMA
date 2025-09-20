<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Truck;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TruckSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = Employee::take(3)->get();

        $trucks = [
            [
                'truck_number' => 'TRK-1001',
                'truck_model' => 'Mercedes Actros',
                'capacity_per_kilo_gram' => 20000.500,
                'is_active' => 1,
            ],
            [
                'truck_number' => 'TRK-1002',
                'truck_model' => 'Volvo FH16',
                'capacity_per_kilo_gram' => 18000.000,
                'is_active' => 1,
            ],
            [
                'truck_number' => 'TRK-1003',
                'truck_model' => 'Scania R500',
                'capacity_per_kilo_gram' => 22000.750,
                'is_active' => 1,
            ],
        ];

        foreach ($employees as $index => $employee) {
            if (isset($trucks[$index])) {
                Truck::create([
                    'driver_id' => $employee->id,
                    'truck_number' => $trucks[$index]['truck_number'],
                    'truck_model' => $trucks[$index]['truck_model'],
                    'capacity_per_kilo_gram' => $trucks[$index]['capacity_per_kilo_gram'],
                    'is_active' => $trucks[$index]['is_active'],
                ]);
            }
        }
    }
}
