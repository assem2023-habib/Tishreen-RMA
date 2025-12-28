<?php

namespace App\Console\Commands;

use App\Enums\ParcelStatus;
use App\Models\Shipment;
use App\Models\Parcel;
use Illuminate\Console\Command;

class ProcessArrivedShipments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipments:process-arrival';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for shipments that have arrived and update linked parcels status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();

        // Find shipments where actual_arrival_time is set and is in the past or present
        $shipments = Shipment::whereNotNull('actual_arrival_time')
            ->where('actual_arrival_time', '<=', $now)
            ->get();

        $updatedCount = 0;

        foreach ($shipments as $shipment) {
            $parcelIds = $shipment->parcelAssignments()->pluck('parcel_id');
            
            // Only update parcels that are currently IN_TRANSIT to READY_FOR_PICKUP
            $parcels = Parcel::whereIn('id', $parcelIds)
                ->where('parcel_status', ParcelStatus::IN_TRANSIT)
                ->get();

            foreach ($parcels as $parcel) {
                $parcel->update([
                    'parcel_status' => ParcelStatus::READY_FOR_PICKUP,
                ]);
                $updatedCount++;
            }
        }

        $this->info("Processed " . $shipments->count() . " shipments. Updated {$updatedCount} parcels to Ready for Pickup.");
    }
}
