<?php

namespace App\Services;

use App\Enums\SenderType;
use App\Models\GuestUser;
use App\Models\ParcelAuthorization;

class AuthorizationService
{
    public function getAllAuthorization($userId)
    {
        $authorizations = ParcelAuthorization::select(
            'id',
            'user_id',
            'parcel_id',
            'authorized_user_id',
            'authorized_user_type',
            'authorized_code',
            'authorized_status',
            'generated_at',
            'expired_at',
            'used_at',
            'cancellation_reason',
        )
            ->where('user_id', $userId)
            ->get();
        return $authorizations;
    }

    public function createAuthorization($data)
    {
        if (!empty($data['authorized_user_id']))
            return $this->createAuthorizationForAuthenticatedUser($data);
        elseif (!empty($data['authorized_guest']))
            return $this->createAuthorizationForGuestedUser($data);
    }
    public function showAuthorization(string $id)
    {
        return ParcelAuthorization::select(
            'id',
            'user_id',
            'parcel_id',
            'authorized_user_id',
            'authorized_user_type',
            'authorized_code',
            'authorized_status',
            'generated_at',
            'expired_at',
            'used_at',
            'cancellation_reason'
        )->where('id', $id)->first();
    }
    public function updateAuthorization($authorizationId, array $data)
    {
        $authorization = ParcelAuthorization::find($authorizationId);
        if (!$authorization) return null;

        if (!empty($data['authorized_user_id'])) {
            $authorization->authorized_user_id = $data['authorized_user_id'];
            $authorization->authorized_user_type = SenderType::AUTHENTICATED_USER->value;
        }

        if (!empty($data['authorized_guest'])) {
            $guestData = $data['authorized_guest'][0];
            $guest = GuestUser::updateOrCreate(
                ['id' => $authorization->authorized_user_type === SenderType::GUEST_USER->value ? $authorization->authorized_user_id : null],
                [
                    'first_name' => $guestData['first_name'] ?? null,
                    'last_name' => $guestData['last_name'] ?? null,
                    'phone' => $guestData['phone'] ?? null,
                    'address' => $guestData['address'] ?? null,
                    'national_number' => $guestData['national_number'] ?? null,
                    'city_id' => $guestData['city_id'] ?? null,
                    'birthday' => $guestData['birthday'] ?? null,
                ]
            );
            $authorization->authorized_user_id = $guest->id;
            $authorization->authorized_user_type = SenderType::GUEST_USER->value;
        }

        if (isset($data['used_at'])) {
            $authorization->used_at = $data['used_at'];
        }
        if (isset($data['cancellation_reason'])) {
            $authorization->cancellation_reason = $data['cancellation_reason'];
        }

        $authorization->save();
        return $authorization->fresh();
    }
    public function deleteAuthorization(string $id)
    {
        $authorization = ParcelAuthorization::find($id);
        if (!$authorization) return false;

        $authorization->delete();
        return true;
    }
    // إنشاء سجل من اجل مستخدم المسند اليه التخويل الذي نوعه مسجل في التطبيق
    private function createAuthorizationForAuthenticatedUser($data)
    {
        $authorizationDetails = ParcelAuthorization::create([
            'user_id' => $data['user_id'],
            'parcel_id' => $data['parcel_id'],
            'authorized_user_id' => $data['authorized_user_id'],
            'authorized_user_type' => SenderType::AUTHENTICATED_USER->value,
        ]);
        return $authorizationDetails;
    }
    // إنشاء سجل من اجل مستخدم المسند اليه التخويل الذي نوعه غير مسجل في التطبيق

    private function createAuthorizationForGuestedUser($data)
    {
        $guestData = $data['authorized_guest'];
        $guestId = GuestUser::create([
            'first_name' => $guestData['first_name'],
            'last_name' => $guestData['last_name'],
            'phone' => $guestData['phone'],
            'address' => $guestData['address'],
            'national_number' => $guestData['national_number'],
            'city_id' => $guestData['city_id'],
            'birthday' => $guestData['birthday'],
        ])->id;
        $authorization = ParcelAuthorization::create([
            'user_id' => $data['user_id'],
            'parcel_id' => $data['parcel_id'],
            'authorized_user_id' => $guestId,
            'authorized_user_type' => SenderType::GUEST_USER->value,
        ]);
        return $authorization;
    }
}
