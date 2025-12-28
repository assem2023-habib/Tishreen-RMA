<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ConfirmAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:confirm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically confirm pending appointments after 10 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = \App\Models\Appointment::where('status', \App\Enums\AppointmentStatus::PENDING)
            ->where('booked_at', '<=', now()->subMinutes(10))
            ->update(['status' => \App\Enums\AppointmentStatus::CONFIRMED]);

        $this->info("Successfully confirmed {$count} appointments.");
    }
}
