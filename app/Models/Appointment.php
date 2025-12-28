<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use App\Notifications\GeneralNotification;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected static function booted()
    {
        static::updated(function ($appointment) {
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
        });
    }
    protected $fillable = [
        'user_id',
        'branch_id', // calender for each branch
        'date',
        'time', // this time is for specific user
        'status',
        'booked_at',
        'booked',
    ];

    protected $casts = [
        'booked_at' => 'datetime',
        'status' => AppointmentStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
