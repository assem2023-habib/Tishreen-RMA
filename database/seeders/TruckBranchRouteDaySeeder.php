<?php

namespace Database\Seeders;

use App\Models\BranchRouteDays;
use App\Models\Truck;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TruckBranchRouteDaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $days = BranchRouteDays::all();
        $trucks = Truck::all();

        if ($days->isEmpty() || $trucks->isEmpty()) {
            $this->command->warn('⚠️ لا يوجد بيانات في branch_route_days أو trucks.');
            return;
        }

        foreach ($days as $day) {
            // نختار شاحنتين عشوائياً لكل يوم (أو أكثر حسب الحاجة)
            $assignedTrucks = $trucks->random(min(1, $trucks->count()));

            foreach ($assignedTrucks as $truck) {
                DB::table('route_day_truck_assignments')->updateOrInsert(
                    [
                        'branch_route_day_id' => $day->id,
                        'truck_id' => $truck->id,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        $this->command->info('✅ تم ربط الشاحنات بالأيام بنجاح.');
    }
}
