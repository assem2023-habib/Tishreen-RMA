<?php

namespace App\Observers;

use App\Enums\OperationTypes;
use App\Models\{Parcel, ParcelHistory};
use Illuminate\Support\Str;

class ParcelObserver
{
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
    public function creating(Parcel $parcel)
    {
        do {
            $tracking_number = 'p' . strtoupper(Str::random(8));
        } while (Parcel::where('tracking_number', $tracking_number)->exists());
        $parcel->tracking_number = $tracking_number;
    }
    public function create(Parcel $parcel)
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
}
