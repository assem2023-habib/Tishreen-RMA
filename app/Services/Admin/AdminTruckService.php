<?php

namespace App\Services\Admin;

use App\Models\Truck;
use Illuminate\Support\Facades\Auth;

class AdminTruckService
{
    public function listTrucks($branchId = null)
    {
        $query = Truck::with(['driver.user']);

        if ($branchId) {
            $query->whereHas('driver', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        return $query->get();
    }

    public function getTruckDetails($id)
    {
        return Truck::with(['driver.user', 'shipments'])->findOrFail($id);
    }

    public function toggleTruckStatus($id)
    {
        $truck = Truck::findOrFail($id);
        $truck->update([
            'is_active' => !$truck->is_active
        ]);

        return $truck->fresh();
    }
}
