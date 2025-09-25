<?php

namespace App\Observers;

use App\Enums\CurrencyType;
use App\Enums\OperationTypes;
use App\Enums\ParcelStatus;
use App\Enums\PolicyTypes;
use App\Enums\PriceUnit;
use App\Models\ParcelShipmentAssignment;
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
        if ($parcel['parcel_status'] === ParcelStatus::CONFIRMED->value) {
            $employeeId = Auth::user()?->employee?->id ?? auth::user()->id;
            if ($employeeId) {
                ParcelShipmentAssignment::create([
                    'parcel_id' => $parcel->id,
                    'shipment_id' => null,
                    'pick_up_confirmed_by_emp_id' => $employeeId,
                    'pick_up_confirmed_date' => now(),
                ]);
            }
        }
    }
    public function updating(Parcel $parcel)
    {
        if (isset($parcel->weight)) {
            $price = $this->resolvePriceByWeight($parcel->weight);
            if ($price) {
                $parcel->cost = $this->calculateCost($parcel->weight, $price);
                $parcel->saveQuietly();
            }
        }
        $changes = $parcel->getChanges();
        if (isset($changes['parcel_status']) && $changes['parcel_status'] === ParcelStatus::CONFIRMED->value) {
            $employeeId = Auth::user()?->employee?->id ?? auth::user()->id;
            dump($employeeId);
            if ($employeeId) {
                ParcelShipmentAssignment::create([
                    'parcel_id' => $parcel->id,
                    'shipment_id' => null,
                    'pick_up_confirmed_by_emp_id' => $employeeId,
                    'pick_up_confirmed_date' => now(),
                ]);
            }
        }
    }
    public function updated(Parcel $parcel)
    {
        $changes = $parcel->getChanges();
        ParcelHistory::create([
            'parcel_id' => $parcel->id,
            'operation_type' => OperationTypes::UPDATED->value,
            'old_data' =>  $parcel->getOriginal(),
            'new_data' => $parcel->toArray(),
            'changes' => $changes,
            'user_id' => Auth::user()->id ?? auth::user()->id ?? $parcel->sender_id,
        ]);
    }

    public function deleting(Parcel $parcel)
    {
        ParcelHistory::create([
            'parcel_id' => $parcel->id,
            'operation_type' => OperationTypes::DELETED->value,
            'user_id' => Auth::user()->id ?? auth::user()->id ?? $parcel->sender_id,
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
