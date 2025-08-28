<?php

namespace App\Http\Requests\Api\V1\Authorization;

use App\Enums\HttpStatus;
use App\Trait\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Client\HttpClientException;

class StoreAuthorizationRequest extends FormRequest
{
    use ApiResponse;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|numeric|exists:users,id|different:authorized_user_id',
            'parcel_id' => 'required|numeric|exists:parcels,id',
            'authorized_user_id' => 'sometimes|numeric|exists:users,id|different:user_id',

            'authorized_guest' => 'sometimes|array',

            'authorized_guest.*.first_name' => 'required|string|max:50',
            'authorized_guest.*.last_name' => 'sometimes|string|max:50',
            'authorized_guest.*.phone' => 'required|string|min:6|max:20|regex:/^\+?\d+$/',
            'authorized_guest.*.address' => 'sometimes|string|max:255',
            'authorized_guest.*.national_number' => 'sometimes|string|max:20',
            'authorized_guest.*.city_id' => 'sometimes|numeric|exists:cities,id',
            'authorized_guest.*.birthday' => 'sometimes|date|before:today',

        ];
    }
    public function messages()
    {
        return [
            'user_id.required' => __('authorization.user_id_required'),
            'user_id.numeric' => __('authorization.user_id_numeric'),
            'user_id.exists' => __('authorization.user_id_exists'),
            'user_id.different' => __('authorization.user_id_different'),

            'parcel_id.required' => __('authorization.parcel_id_required'),
            'parcel_id.numeric' => __('authorization.parcel_id_numeric'),
            'parcel_id.exists' => __('authorization.parcel_id_exists'),

            'authorized_user_id.numeric' => __('authorization.authorized_user_id_numeric'),
            'authorized_user_id.exists' => __('authorization.authorized_user_id_exists'),
            'authorized_user_id.different' => __('authorization.authorized_user_id_different'),

            // authorized_guest

            'authorized_guest.array' => __('authorization.authorized_guest_array'),
            'authorized_guest.prohibited_with' => __('authorization.authorized_guest_prohibited_with'),

            // authorized_guest.* fields
            'authorized_guest.*.first_name.required' => __('authorization.guest_first_name_required'),
            'authorized_guest.*.first_name.string' => __('authorization.guest_first_name_string'),
            'authorized_guest.*.first_name.max' => __('authorization.guest_first_name_max'),

            'authorized_guest.*.last_name.string' => __('authorization.guest_last_name_string'),
            'authorized_guest.*.last_name.max' => __('authorization.guest_last_name_max'),

            'authorized_guest.*.phone.required' => __('authorization.guest_phone_required'),
            'authorized_guest.*.phone.string' => __('authorization.guest_phone_string'),
            'authorized_guest.*.phone.min' => __('authorization.guest_phone_min'),
            'authorized_guest.*.phone.max' => __('authorization.guest_phone_max'),
            'authorized_guest.*.phone.regex' => __('authorization.guest_phone_regex'),

            'authorized_guest.*.address.string' => __('authorization.guest_address_string'),
            'authorized_guest.*.address.max' => __('authorization.guest_address_max'),

            'authorized_guest.*.national_number.string' => __('authorization.guest_national_number_string'),
            'authorized_guest.*.national_number.max' => __('authorization.guest_national_number_max'),

            'authorized_guest.*.city_id.numeric' => __('authorization.guest_city_id_numeric'),
            'authorized_guest.*.city_id.exists' => __('authorization.guest_city_id_exists'),

            'authorized_guest.*.birthday.date' => __('authorization.guest_birthday_date'),
            'authorized_guest.*.birthday.before' => __('authorization.guest_birthday_before_today'),
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpClientException(
            $this->errorResponse(
                "cannot create Authorization",
                HttpStatus::BAD_REQUEST->value,
                $validator->errors()
            )
        );
    }
}
