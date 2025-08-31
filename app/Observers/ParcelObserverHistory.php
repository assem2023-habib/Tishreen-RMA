<?php

namespace App\Observers;

use App\Enums\OperationTypes;
use App\Enums\PolicyTypes;
use App\Models\PricingPolicy;
use App\Models\{Parcel, ParcelHistory};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ParcelObserverHistory
{
    /**
     * Handle the Parcel "created" event.
     */
    public function creating(Parcel $parcel)
    {
        $parcel->tracking_number = $this->generateUniqueTrackingNumber();
        $parcel->cost = $this->calculateCostByPricePolicy($parcel->weight, $parcel->price);
    }
    public function updating(Parcel $parcel)
    {
        if (!isset($parcel->weight)) {
            return;
        }
        $pricePolicy =  PricingPolicy::select('id', 'price')
            ->where('policy_type', PolicyTypes::WEIGHT->value)
            ->where('limit_min', '<=', $parcel['weight'])
            ->where('limit_max', '>=', $parcel['weight'])
            ->first();
        if ($pricePolicy) {
            $parcel->cost = $this->calculateCostByPricePolicy($parcel->weight, $pricePolicy->price);
            $parcel->saveQuietly();
        }
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
                'user_id' => Auth::user()->id,
            ]
        );
    }
    public function deleting(Parcel $parcel)
    {
        ParcelHistory::create([
            'parcel_id' => $parcel->id,
            'operation_type' => OperationTypes::DELETED,
            'user_id' => Auth::user()->id,
            'new_data' => null,
            'old_data' => $parcel->toArray(),
            'changes' => $parcel->getChanges(),
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
