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

        // Logic to determine current location based on status
        // This is a simplified logic, you might want to adjust it based on your workflow
        $locationData = [
            'tracking_number' => $parcel->tracking_number,
            'status' => $parcel->parcel_status,
            'current_location' => null,
            'latitude' => null,
            'longitude' => null,
        ];

        switch ($parcel->parcel_status) {
            case ParcelStatus::PENDING->value:
            case ParcelStatus::CONFIRMED->value:
                if ($parcel->route && $parcel->route->fromBranch) {
                    $locationData['current_location'] = $parcel->route->fromBranch->branch_name;
                    $locationData['latitude'] = $parcel->route->fromBranch->latitude;
                    $locationData['longitude'] = $parcel->route->fromBranch->longitude;
                }
                break;
            case ParcelStatus::DELIVERED->value:
                if ($parcel->route && $parcel->route->toBranch) {
                    $locationData['current_location'] = $parcel->route->toBranch->branch_name;
                    $locationData['latitude'] = $parcel->route->toBranch->latitude;
                    $locationData['longitude'] = $parcel->route->toBranch->longitude;
                }
                break;
            case ParcelStatus::IN_TRANSIT->value:
            case ParcelStatus::OUT_FOR_DELIVERY->value:
                // In a real system, you might have real-time truck coordinates.
                // For now, we return the destination or mid-point logic.
                if ($parcel->route && $parcel->route->toBranch) {
                    $locationData['current_location'] = "In Transit to " . $parcel->route->toBranch->branch_name;
                    $locationData['latitude'] = $parcel->route->toBranch->latitude;
                    $locationData['longitude'] = $parcel->route->toBranch->longitude;
                }
                break;
            default:
                $locationData['current_location'] = "Unknown or processing";
                break;
        }

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
