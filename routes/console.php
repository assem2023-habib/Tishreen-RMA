<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:confirm-appointments')->everyMinute();
Schedule::command('app:remind-pickup-parcels')->daily();
Schedule::command('shipments:process-arrival')->hourly();
