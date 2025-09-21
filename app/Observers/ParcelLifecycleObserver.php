<?php

namespace App\Observers;

use App\Enums\CurrencyType;
use App\Enums\OperationTypes;
use App\Enums\PolicyTypes;
use App\Enums\PriceUnit;
use App\Models\PricingPolicy;
use App\Models\{Parcel, ParcelHistory};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ParcelLifecycleObserver
{
    /**
     * Handle the Parcel "created" event.
     */
    public function creating(Parcel $parcel)
    {
        $parcel->tracking_number = $this->generateUniqueTrackingNumber();
        $pricePolicy = $this->resolvePriceByWeight($parcel->weight);
        $parcel->cost = $this->calculateCost($parcel->weight, $pricePolicy);
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
            $parcel->cost = $this->calculateCost($parcel->weight, $pricePolicy->price);
            $parcel->saveQuietly();
        }
        $changes = $parcel->getChanges();
        ParcelHistory::create([
            'parcel_id' => $parcel->id,
            'operation_type' => OperationTypes::CREATED->value,
            'old_data' =>  $parcel->getOriginal(),
            'new_data' => $parcel->toArray(),
            'changes' => $changes,
            'user_id' => Auth::user()->id,
        ]);
    }
    public function created(Parcel $parcel)
    {
        ParcelHistory::create(
            [
                'parcel_id' => $parcel->id,
                'operation_type' => OperationTypes::CREATED->value,
                'new_data' => $parcel->toArray(),
                'user_id' => Auth::user()->id ?? $parcel->sender_id,
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
    private function calculateCost($weight, $pricePolicy)
    {
        return $weight * $pricePolicy;
    }
    private function resolvePriceByWeight($weight)
    {
        $policy = PricingPolicy::select(
            'policy_type',
            'price',
            'price_unit',
            'limit_max',
            'limit_min',
            'currency',
            'is_active'
        )
            ->where('price_unit', PriceUnit::KG->value)
            ->where('policy_type', PolicyTypes::WEIGHT->value)
            ->where('limit_min', '<=', $weight)
            ->where('limit_max', '>=', $weight)
            ->where('currency', CurrencyType::SYRIA->value)
            ->where('is_active', 1)
            ->first()
            ->price;
        return $policy;
    }
}
