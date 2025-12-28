<?php

namespace App\Observers;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Notifications\GeneralNotification;

class AppointmentObserver
{
    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        if ($appointment->isDirty('status') && $appointment->status === AppointmentStatus::CONFIRMED) {
            if ($appointment->user) {
                $appointment->user->notify(new GeneralNotification(
                    'تأكيد الموعد',
                    "تم تأكيد موعدك بنجاح في تاريخ {$appointment->date} الساعة {$appointment->time}",
                    'appointment_confirmed',
                    ['appointment_id' => $appointment->id]
                ));
            }
        }
    }
}
