<?php

namespace App\Services;

use App\Models\BranchRoute;
use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentHepler
{
    public static function generateForRoute($routeId)
    {
        $route = BranchRoute::findOrFail($routeId);

        // وقت الوصول المتوقع للرحلة
        $arrival = Carbon::parse($route->estimated_arrival_time);

        // البداية: اليوم التالي لوصول الرحلة
        $startDate = $arrival->copy()->addDay();
        $endDate = $startDate->copy()->addDays(7); // لمدة 7 أيام

        $slotDuration = 15; // مدة كل ربع ساعة
        $workingStart = Carbon::parse('08:00'); // وقت عمل الفرع
        $workingEnd = Carbon::parse('15:00');
        $capacityPerSlot = 1;

        for ($date = $startDate->copy(); $date < $endDate; $date->addDay()) {
            $currentTime = $date->copy()->setTimeFrom($workingStart);

            while ($currentTime < $date->copy()->setTimeFrom($workingEnd)) {
                Appointment::updateOrCreate(
                    [
                        'branch_id' => $route->to_branch_id,
                        'route_id' => $route->id,
                        'date' => $date->format('Y-m-d'),
                        'time' => $currentTime->format('H:i:s'),
                    ],
                    [
                        'capacity' => $capacityPerSlot,
                        'booked_count' => 0,
                    ]
                );

                $currentTime->addMinutes($slotDuration);
            }
        }
    }
}
