<?php

namespace App\Services\Admin;

use App\Models\Parcel;
use App\Enums\ParcelStatus;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class AdminParcelService
{
    public function listAllParcels($branchId = null)
    {
        $query = Parcel::with(['route.fromBranch', 'route.toBranch', 'sender']);

        if ($branchId) {
            $query->whereHas('route', function ($q) use ($branchId) {
                $q->where('from_branch_id', $branchId)
                    ->orWhere('to_branch_id', $branchId);
            });
        }

        return $query->paginate(15);
    }

    public function getParcelHistory($parcelId)
    {
        $parcel = Parcel::with(['parcelsHistories.user'])->findOrFail($parcelId);
        return $parcel->parcelsHistories;
    }

    public function confirmParcelReception($parcelId)
    {
        $parcel = Parcel::findOrFail($parcelId);

        if ($parcel->parcel_status !== ParcelStatus::PENDING->value) {
            return [
                'status' => false,
                'message' => 'Parcel is not in PENDING status.'
            ];
        }

        $parcel->update([
            'parcel_status' => ParcelStatus::CONFIRMED->value
        ]);

        return [
            'status' => true,
            'parcel' => $parcel->fresh()
        ];
    }

    public function updateStatus($parcelId, $status)
    {
        $parcel = Parcel::findOrFail($parcelId);

        // Ensure status is valid from Enum
        if (!in_array($status, ParcelStatus::values())) {
            return [
                'status' => false,
                'message' => 'Invalid status.'
            ];
        }

        $parcel->update([
            'parcel_status' => $status
        ]);

        return [
            'status' => true,
            'parcel' => $parcel->fresh()
        ];
    }
}
