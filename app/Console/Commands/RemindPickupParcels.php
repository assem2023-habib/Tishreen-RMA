<?php

namespace App\Console\Commands;

use App\Enums\ParcelStatus;
use App\Enums\SenderType;
use App\Models\Parcel;
use App\Notifications\GeneralNotification;
use Illuminate\Console\Command;

class RemindPickupParcels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remind-pickup-parcels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إرسال إشعارات تذكير للطرود الجاهزة للاستلام والتي مضى عليها أكثر من 3 أيام';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Fetch parcels ready for pickup that were updated more than 3 days ago
        $parcels = Parcel::where('parcel_status', ParcelStatus::READY_FOR_PICKUP)
            ->where('updated_at', '<=', now()->subDays(3))
            ->get();

        foreach ($parcels as $parcel) {
            $user = null;
            if ($parcel->sender_type === SenderType::AUTHENTICATED_USER) {
                $user = $parcel->sender;
            }

            if ($user) {
                $user->notify(new GeneralNotification(
                    'تذكير باستلام الطرد',
                    "طردك رقم {$parcel->tracking_number} جاهز للاستلام منذ فترة. يرجى مراجعة المركز لاستلامه.",
                    'pickup_reminder',
                    ['parcel_id' => $parcel->id, 'tracking_number' => $parcel->tracking_number]
                ));
            }
        }

        $this->info("Sent " . $parcels->count() . " pickup reminders.");
    }
}
