<?php

namespace App\Services;

use App\Enums\ParcelStatus;
use App\Enums\PolicyTypes;
use App\Enums\SenderType;
use App\Models\Parcel;
use App\Models\PricingPolicy;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ParcelService
{
    public function showParcels($userId)
    {
        $parcelsData =
            Parcel::select(
                'id',
                'sender_id',
                'sender_type',
                'route_id',
                'reciver_name',
                'reciver_address',
                'reciver_phone',
                'weight',
                'cost',
                'is_paid',
                'parcel_status',
                'tracking_number',
            )
            ->where('sender_id', $userId)
            ->where('sender_type', SenderType::AUTHENTICATED_USER->value)
            ->get();
        return $parcelsData;
    }
    public function createParcel($data)
    {
        // $policy = PricingPolicy::select('id', 'price')
        //     ->where('policy_type', PolicyTypes::WEIGHT->value)
        //     ->where('limit_min', '<=', $data['weight'])
        //     ->where('limit_max', '>=', $data['weight'])
        //     ->first(); //remove because this section add in ParcelObserveHistory

        $parcel = Parcel::create([
            'sender_id' => Auth::user()->id,
            'sender_type' => SenderType::AUTHENTICATED_USER->value,
            'route_id' => $data['route_id'],
            'reciver_name' => $data['reciver_name'],
            'reciver_address' => $data['reciver_address'],
            'reciver_phone' => $data['reciver_phone'],
            'weight' => $data['weight'],
            'is_paid' => $data['is_paid'] ? 1 : 0,
            'parcel_status' => ParcelStatus::PENDING->value,
            // 'price_policy_id' => $policy->price, // remove from data base
        ]);
        return $parcel->fresh();
    }


    public function updateParcel($parcelId, $parcel)
    {
        $parcelModel = Parcel::where('id', $parcelId)
            ->where('sender_id', Auth::user()->id)
            ->where('sender_type', SenderType::AUTHENTICATED_USER->value)
            ->first();

        if (!$parcelModel) {
            return [
                'status' => false,
                'message' => __('parcel.no_parcel_found'),
            ];
        }

        if ($parcelModel->parcel_status != ParcelStatus::PENDING->value) {
            return [
                'status' => false,
                'message' => __('parcel.parcel_not_pending'),
            ];
        }

        $createdAt = Carbon::parse($parcelModel->created_at);
        $now = Carbon::now();
        if ($createdAt->diffInDays($now) > 7) {
            return [
                'status' => false,
                'message' => __('parcel.parcel_expired'),
            ];
        }

        $parcelModel->update($parcel);

        return [
            'status' => true,
            'message' => __('parcel.parcel_updated_successfuly'),
            'parcel' => $parcelModel,
        ];
    }
    public function showParcel($parcelId)
    {
        $parcelData = Parcel::select(
            'id',
            'sender_id',
            'sender_type',
            'route_id',
            'reciver_name',
            'reciver_address',
            'reciver_phone',
            'weight',
            'cost',
            'is_paid',
            'parcel_status',
            'tracking_number',
        )
            ->where('id', $parcelId)
            ->first();
        return $parcelData;
    }

    public function getParcelLocation($tracking_number)
    {
        $parcel = Parcel::where('tracking_number', $tracking_number)
            ->with(['route.fromBranch', 'route.toBranch'])
            ->first();

        if (!$parcel) {
            return [
                'status' => false,
                'message' => __('parcel.no_parcel_found'),
            ];
        }

        // Determine current location coordinates based on status
        $lat = null;
        $lng = null;

        switch ($parcel->parcel_status) {
            case ParcelStatus::PENDING->value:
            case ParcelStatus::CONFIRMED->value:
                $lat = $parcel->route->fromBranch->latitude ?? null;
                $lng = $parcel->route->fromBranch->longitude ?? null;
                break;
            case ParcelStatus::DELIVERED->value:
            case ParcelStatus::IN_TRANSIT->value:
            case ParcelStatus::OUT_FOR_DELIVERY->value:
            case ParcelStatus::READY_FOR_PICKUP->value:
                $lat = $parcel->route->toBranch->latitude ?? null;
                $lng = $parcel->route->toBranch->longitude ?? null;
                break;
            default:
                $lat = $parcel->route->fromBranch->latitude ?? null;
                $lng = $parcel->route->fromBranch->longitude ?? null;
                break;
        }

        $locationData = [
            'tracking_number' => $parcel->tracking_number,
            'status' => $parcel->parcel_status,
            'current_location' => [
                'latitude' => $lat,
                'longitude' => $lng,
                'last_updated' => $parcel->updated_at->format('Y-m-d H:i:s'),
            ],
            'source_branch' => $parcel->route && $parcel->route->fromBranch ? [
                'id' => $parcel->route->fromBranch->id,
                'branch_name' => $parcel->route->fromBranch->branch_name,
                'address' => $parcel->route->fromBranch->address,
                'latitude' => $parcel->route->fromBranch->latitude,
                'longitude' => $parcel->route->fromBranch->longitude,
            ] : null,
            'destination_branch' => $parcel->route && $parcel->route->toBranch ? [
                'id' => $parcel->route->toBranch->id,
                'branch_name' => $parcel->route->toBranch->branch_name,
                'address' => $parcel->route->toBranch->address,
                'latitude' => $parcel->route->toBranch->latitude,
                'longitude' => $parcel->route->toBranch->longitude,
            ] : null,
        ];

        return [
            'status' => true,
            'data' => $locationData,
        ];
    }
    public function deleteParcel($userId, $parcelId)
    {
        $parcelDeleted = Parcel::where('sender_id', $userId)
            ->where('sender_type', SenderType::AUTHENTICATED_USER->value)
            ->where('id', $parcelId)
            ->first();
        if (!$parcelDeleted)
            return false;
        $parcelDeleted->delete();
        return true;
    }
}
