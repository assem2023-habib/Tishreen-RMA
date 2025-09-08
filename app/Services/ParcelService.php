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
        $policy = PricingPolicy::select('id', 'price')
            ->where('policy_type', PolicyTypes::WEIGHT->value)
            ->where('limit_min', '<=', $data['weight'])
            ->where('limit_max', '>=', $data['weight'])
            ->first();

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
            'price_policy_id' => $policy->price,
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
            ->where('id', $parcelId,)
            ->where('sender_id', Auth::user()->id)
            ->where('sender_type', SenderType::AUTHENTICATED_USER->value)
            ->first();
        return $parcelData;
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
