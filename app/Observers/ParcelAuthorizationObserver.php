<?php

namespace App\Observers;

use App\Models\ParcelAuthorization;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ParcelAuthorizationObserver
{
    /**
     * Handle the ParcelAuthorization "created" event.
     */
    public function creating(ParcelAuthorization $parcelAuthorization): void
    {
        do {
            $authorized_code = strtoupper(Str::random(10));
        } while (ParcelAuthorization::where('authorized_code', $authorized_code)->exists());
        $parcelAuthorization->authorized_code = $authorized_code;

        $parcelAuthorization->expired_at = Carbon::parse($parcelAuthorization->generated_at)->addDays(7);
    }
    /**
     * Handle the ParcelAuthorization "updated" event.
     */
    public function updated(ParcelAuthorization $parcelAuthorization): void
    {
        //
    }

    /**
     * Handle the ParcelAuthorization "deleted" event.
     */
    public function deleted(ParcelAuthorization $parcelAuthorization): void
    {
        //
    }

    /**
     * Handle the ParcelAuthorization "restored" event.
     */
    public function restored(ParcelAuthorization $parcelAuthorization): void
    {
        //
    }

    /**
     * Handle the ParcelAuthorization "force deleted" event.
     */
    public function forceDeleted(ParcelAuthorization $parcelAuthorization): void
    {
        //
    }
}
