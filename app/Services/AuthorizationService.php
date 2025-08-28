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
    public function showAuthorization(string $id) {
        
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
        $guestId = GuestUser::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'national_number' => $data['national_number'],
            'city_id' => $data['city_id'],
            'birthday' => $data['birthday'],
        ])['id'];
        $authorization = ParcelAuthorization::create([
            'user_id' => $data['user_id'],
            'parcel_id' => $data['parcel_id'],
            'authorized_user_id' => $guestId,
            'authorized_user_type' => SenderType::GUEST_USER->value,
        ]);
        return $authorization;
    }
}
