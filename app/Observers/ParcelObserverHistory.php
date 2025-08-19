<?php

namespace App\Observers;

use App\Enums\OperationTypes;
use App\Models\PricingPolicy;
use App\Models\{Parcel, ParcelHistory};
use Illuminate\Support\Str;

class ParcelObserverHistory
{
    /**
     * Handle the Parcel "created" event.
     */
    public function creating(Parcel $parcel)
    {
        $parcel->tracking_number = $this->generateUniqueTrackingNumber();

        $price = PricingPolicy::select('id', 'price')->where('id', $parcel->price_policy_id)->first();
        $parcel->cost = $this->calculateCostByPricePolicy($parcel->weight, $price->price);
    }
    public function updated(Parcel $parcel)
    {
        $changes = $parcel->getChanges();
        ParcelHistory::create([
            'parcel_id' => $parcel->id,
            'operation_type' => OperationTypes::CREATED->value,
            'old_data' =>  $parcel->getOriginal(),
            'new_data' => $parcel->toArray(),
            'changes' => $changes,
            'user_id' => auth()->id(),
        ]);
    }
    public function created(Parcel $parcel)
    {
        ParcelHistory::create(
            [
                'parcel_id' => $parcel->id,
                'operation_type' => OperationTypes::CREATED->value,
                'new_data' => $parcel->toArray(),
                'user_id' => auth()->id(),
            ]
        );
    }
    public function deleted(Parcel $parcel)
    {
        ParcelHistory::create([
            'parcel_id' => $parcel->id,
            'operation_type' => OperationTypes::DELETED,
            'new_data' => null,
            'old_data' => $parcel->toArray(),
        ]);
    }
    private function generateUniqueTrackingNumber(): string
    {
        do {
            $tracking = strtoupper(Str::random(10));
        } while (Parcel::where('tracking_number', $tracking)->exists());
        return $tracking;
    }
    private function calculateCostByPricePolicy($weight, $pricePolicy)
    {
        return $weight * $pricePolicy;
    }
}
