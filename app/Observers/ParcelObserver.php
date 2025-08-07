<?php

namespace App\Observers;

use App\Enums\OperationTypes;
use App\Models\{Parcel, ParcelHistory};

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
