<?php

namespace App\Services\Admin;

use App\Models\Shipment;
use App\Models\Parcel;
use App\Enums\ParcelStatus;
use Carbon\Carbon;

class AdminShipmentService
{
    public function listShipments($branchId = null)
    {
        $query = Shipment::with(['branchRouteDay.branchRoute.fromBranch', 'branchRouteDay.branchRoute.toBranch', 'trucks']);

        if ($branchId) {
            $query->whereHas('branchRouteDay.branchRoute', function ($q) use ($branchId) {
                $q->where('from_branch_id', $branchId)
                  ->orWhere('to_branch_id', $branchId);
            });
        }

        return $query->paginate(15);
    }

    public function departShipment($shipmentId)
    {
        $shipment = Shipment::findOrFail($shipmentId);

        if ($shipment->actual_departure_time !== null) {
            return [
                'status' => false,
                'message' => 'Shipment has already departed.'
            ];
        }

        $shipment->update([
            'actual_departure_time' => Carbon::now(),
        ]);

        // Update linked parcels to IN_TRANSIT
        $parcelIds = $shipment->parcelAssignments()->pluck('parcel_id');
        Parcel::whereIn('id', $parcelIds)
            ->where('parcel_status', ParcelStatus::CONFIRMED->value)
            ->update(['parcel_status' => ParcelStatus::IN_TRANSIT->value]);

        return [
            'status' => true,
            'shipment' => $shipment->fresh()
        ];
    }

    public function arriveShipment($shipmentId)
    {
        $shipment = Shipment::findOrFail($shipmentId);

        if ($shipment->actual_departure_time === null) {
            return [
                'status' => false,
                'message' => 'Shipment has not departed yet.'
            ];
        }

        if ($shipment->actual_arrival_time !== null) {
            return [
                'status' => false,
                'message' => 'Shipment has already arrived.'
            ];
        }

        $shipment->update([
            'actual_arrival_time' => Carbon::now(),
        ]);

        // Update linked parcels to READY_FOR_PICKUP
        $parcelIds = $shipment->parcelAssignments()->pluck('parcel_id');
        Parcel::whereIn('id', $parcelIds)
            ->where('parcel_status', ParcelStatus::IN_TRANSIT->value)
            ->update(['parcel_status' => ParcelStatus::READY_FOR_PICKUP->value]);

        return [
            'status' => true,
            'shipment' => $shipment->fresh()
        ];
    }
}
