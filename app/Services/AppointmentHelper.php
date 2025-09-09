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
            // get today date and add 1 day to stat generation calender
            $startDate = Carbon::tomorrow();
            $endDate = $startDate->copy()->addDays(6);

            // for each day
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {

                $startTime = Carbon::createFromTime(8, 0);
                $endTime   = Carbon::createFromTime(15, 0);

                while ($startTime < $endTime) {
                    Appointment::firstOrCreate([
                        'branch_id' => $branchId,
                        'user_id'   => $userId,
                        'date'      => $date->toDateString(),
                        'time'      => $startTime->format('H:i:s'),
                    ], [
                        'booked' => false,
                    ]);

                    // next appointment is after 15 min
                    $startTime->addMinutes(15);
                }
            }
        });
    }
}
