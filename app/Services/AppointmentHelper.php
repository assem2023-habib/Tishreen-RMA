<?php

namespace App\Services;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentHelper
{
    public static function generateWeeklyAppointments($branchId, $userId = null)
    {
        DB::transaction(function () use ($branchId, $userId) {
            // اليوم الحالي (نولّد من يوم الغد لأسبوع كامل)
            $startDate = Carbon::tomorrow();
            $endDate = $startDate->copy()->addDays(6);

            // نكرر لكل يوم
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {

                $startTime = Carbon::createFromTime(8, 0);  // 08:00 صباحًا
                $endTime   = Carbon::createFromTime(15, 0); // 03:00 عصرًا

                while ($startTime < $endTime) {
                    Appointment::firstOrCreate([
                        'branch_id' => $branchId,
                        'user_id'   => $userId,
                        'date'      => $date->toDateString(),
                        'time'      => $startTime->format('H:i:s'),
                    ], [
                        'booked' => false,
                    ]);

                    // نضيف 15 دقيقة
                    $startTime->addMinutes(15);
                }
            }
        });
    }
}
