<?php

namespace App\Services;

use App\Enums\HttpStatus;
use App\Enums\SenderType;
use App\Models\GuestUser;
use App\Models\ParcelAuthorization;
use App\Trait\ApiResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AuthorizationService
{
    use ApiResponse;
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
        $existsQuery = ParcelAuthorization::where('user_id', Auth::user()->id)
            ->where('parcel_id', $data['parcel_id']);

        if (!empty($data['authorized_user_id'])) {
            $existsQuery->where('authorized_user_id', $data['authorized_user_id'])
                ->where('authorized_user_type', SenderType::AUTHENTICATED_USER->value);
        } elseif (!empty($data['authorized_guest'])) {
            // نفترض عنصر واحد في المصفوفة
            $guestPhone = $data['authorized_guest']['phone'] ?? null;
            if ($guestPhone) {
                $guest = GuestUser::where('phone', $guestPhone)->first();
                if ($guest) {
                    $existsQuery->where('authorized_user_id', $guest->id)
                        ->where('authorized_user_type', SenderType::GUEST_USER->value);
                }
            }
        }

        if ($existsQuery->exists()) {
            return null;
        }
        if (!empty($data['authorized_user_id']))
            return $this->createAuthorizationForAuthenticatedUser($data);
        elseif (!empty($data['authorized_guest']))
            return $this->createAuthorizationForGuestedUser($data);
        return null;
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

        if (!$authorization) {
            return [
                'status' => false,
                'message' => __('authorization.cannot_update_authorization'),
            ];
        }

        // إذا تم استخدام التخويل بالفعل
        if (!is_null($authorization->used_at)) {
            return [
                'status' => false,
                'message' => __('authorization.authorization_already_used'),

            ];
        }

        // إذا انقضت مدة 7 أيام على إنشاء التخويل
        $createdAt = Carbon::parse($authorization->generated_at);
        $now = Carbon::now();
        if ($createdAt->diffInDays($now) > 7) {
            return [
                'status' => false,
                'message' => __('authorization.authorization_expired'),
            ];
        }

        // تحديث المستخدم المفوض إذا كان معرف المستخدم موجود
        if (!empty($data['authorized_user_id'])) {
            $authorization->authorized_user_id = $data['authorized_user_id'];
            $authorization->authorized_user_type = SenderType::AUTHENTICATED_USER->value;
        }

        // تحديث الضيف إذا كان موجود
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

        // تحديث الحقول الأخرى
        if (isset($data['used_at'])) {
            $authorization->used_at = $data['used_at'];
        }
        if (isset($data['cancellation_reason'])) {
            $authorization->cancellation_reason = $data['cancellation_reason'];
        }

        $authorization->save();

        return [
            'status' => true,
            'message' => __('authorization.authorization_updated_successfully'),
            'authorization' => $authorization->fresh(),
        ];
    }
    public function deleteAuthorization(string $id)
    {
        $authorization = ParcelAuthorization::find($id);
        if (!$authorization) return false;

        $authorization->delete();
        return true;
    }

    public function useAuthorization(string $authorizationId)
    {
        $authorization = ParcelAuthorization::find($authorizationId);

        if (!$authorization) {
            return [
                'status' => false,
                'message' => __('authorization.no_authorization_found'),
            ];
        }

        // إذا تم استخدام التخويل مسبقًا
        if ($authorization->used_at) {
            return [
                'status' => false,
                'message' => __('authorization.authorization_already_used'),
            ];
        }

        $authorization->used_at = Carbon::now(); // التاريخ الفعلي
        $authorization->save();

        return [
            'status' => true,
            'message' => __('authorization.authorization_used_successfully'),
            'authorization' => $authorization,
        ];
    }
    // إنشاء سجل من اجل مستخدم المسند اليه التخويل الذي نوعه مسجل في التطبيق
    private function createAuthorizationForAuthenticatedUser($data)
    {
        $authorizationDetails = ParcelAuthorization::create([
            'user_id' => Auth::user()->id,
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
            'user_id' => Auth::user()->id,
            'parcel_id' => $data['parcel_id'],
            'authorized_user_id' => $guestId,
            'authorized_user_type' => SenderType::GUEST_USER->value,
        ]);
        return $authorization;
    }
}
