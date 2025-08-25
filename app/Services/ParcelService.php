<?php

use App\Enums\ParcelStatus;
use App\Enums\SenderType;
use App\Models\Parcel;

class ParcelService
{
    public function createParcel($data)
    {
        $parcel = Parcel::create([
            'sender_id' => $data['sender_id'],
            'sender_type' => SenderType::AUTHENTICATED_USER->value,
            'route_id' => $data['route_id'],
            'reciver_name' => $data['reciver_name'],
            'reciver_address' => $data['reciver_address'],
            'reciver_phone' => $data['reciver_phone'],
            'weight' => $data['weight'],
            'is_paid' => $data['is_paid'] ? 1 : 0,
            'parcel_status' => ParcelStatus::PENDING->value,
            'price_policy_id' => $data['price_policy_id'],
        ]);
        return $parcel;
    }
    public function showParcel($userId, $parcelId)
    {
        $parcelData = Parcel::select(
            'sender_id',
            'route_id',
            'reciver_name',
            'reciver_address',
            'reciver_phone',
            'weight',
            'is_paid',
            'parcel_status',
            'tracking_number',
            'price_policy_id',
        )
            ->where('id', $parcelId)
            ->where('sender_id', $userId)
            ->where('sender_type', SenderType::AUTHENTICATED_USER->value)
            ->first();

        return $parcelData;
    }
    public function showParcels($userId)
    {
        $parcelsData =
            Parcel::select(
                'sender_id',
                'route_id',
                'reciver_name',
                'reciver_address',
                'reciver_phone',
                'weight',
                'is_paid',
                'parcel_status',
                'tracking_number',
                'price_policy_id',
            )
            ->where('sender_id', $userId)
            ->where('sender_type', SenderType::AUTHENTICATED_USER->value)
            ->get();
        return $parcelsData;
    }
    public function deleteParcel($userId, $parcelId)
    {
        $parcelDeleted = Parcel::where('sender_id', $userId)
            ->where('id', $parcelId)
            ->first();
        if (!$parcelDeleted)
            return false;
        $parcelDeleted->delete();
        return true;
    }
}
