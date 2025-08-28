<?php

namespace App\Http\Requests\Api\V1\Authorization;

use App\Enums\HttpStatus;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Client\HttpClientException;

class UpdateAuthorizationRequest extends FormRequest
{
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
            'user_id' => 'sometimes|numeric|exists:users,id|different:authorized_user_id',
            'parcel_id' => 'sometimes|numeric|exists:parcels,id',

            'authorized_user_id' => 'sometimes|numeric|exists:users,id|different:user_id|prohibited_with:authorized_guest',
            'authorized_guest' => 'sometimes|array|prohibited_with:authorized_user_id',

            'authorized_guest.*.first_name' => 'required_with:authorized_guest|string|max:50',
            'authorized_guest.*.last_name' => 'sometimes|string|max:50',
            'authorized_guest.*.phone' => 'required_with:authorized_guest|string|min:6|max:20|regex:/^\+?\d+$/',
            'authorized_guest.*.address' => 'sometimes|string|max:255',
            'authorized_guest.*.national_number' => 'sometimes|string|max:20',
            'authorized_guest.*.city_id' => 'sometimes|numeric|exists:cities,id',
            'authorized_guest.*.birthday' => 'sometimes|date|before:today',

            'used_at' => 'sometimes|date|nullable',
            'cancellation_reason' => 'sometimes|string|nullable',
        ];
    }
    public function messages()
    {
        return [
            'user_id.numeric' => __('authorization.user_id_numeric'),
            'user_id.exists' => __('authorization.user_id_exists'),
            'user_id.different' => __('authorization.user_id_different'),

            'parcel_id.numeric' => __('authorization.parcel_id_numeric'),
            'parcel_id.exists' => __('authorization.parcel_id_exists'),

            'authorized_user_id.numeric' => __('authorization.authorized_user_id_numeric'),
            'authorized_user_id.exists' => __('authorization.authorized_user_id_exists'),
            'authorized_user_id.different' => __('authorization.authorized_user_id_different'),
            'authorized_user_id.prohibited_with' => __('authorization.authorized_user_id_prohibited_with'),

            'authorized_guest.array' => __('authorization.authorized_guest_array'),
            'authorized_guest.prohibited_with' => __('authorization.authorized_guest_prohibited_with'),

            'authorized_guest.*.first_name.required_with' => __('authorization.authorized_guest_first_name_required'),
            'authorized_guest.*.first_name.string' => __('authorization.authorized_guest_first_name_string'),
            'authorized_guest.*.first_name.max' => __('authorization.authorized_guest_first_name_max'),

            'authorized_guest.*.last_name.string' => __('authorization.authorized_guest_last_name_string'),
            'authorized_guest.*.last_name.max' => __('authorization.authorized_guest_last_name_max'),

            'authorized_guest.*.phone.required_with' => __('authorization.authorized_guest_phone_required'),
            'authorized_guest.*.phone.string' => __('authorization.authorized_guest_phone_string'),
            'authorized_guest.*.phone.min' => __('authorization.authorized_guest_phone_min'),
            'authorized_guest.*.phone.max' => __('authorization.authorized_guest_phone_max'),
            'authorized_guest.*.phone.regex' => __('authorization.authorized_guest_phone_regex'),

            'authorized_guest.*.address.string' => __('authorization.authorized_guest_address_string'),
            'authorized_guest.*.address.max' => __('authorization.authorized_guest_address_max'),

            'authorized_guest.*.national_number.string' => __('authorization.authorized_guest_national_number_string'),
            'authorized_guest.*.national_number.max' => __('authorization.authorized_guest_national_number_max'),

            'authorized_guest.*.city_id.numeric' => __('authorization.authorized_guest_city_id_numeric'),
            'authorized_guest.*.city_id.exists' => __('authorization.authorized_guest_city_id_exists'),

            'authorized_guest.*.birthday.date' => __('authorization.authorized_guest_birthday_date'),
            'authorized_guest.*.birthday.before' => __('authorization.authorized_guest_birthday_before'),

            'used_at.date' => __('authorization.used_at_date'),
            'cancellation_reason.string' => __('authorization.cancellation_reason_string'),
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpClientException(
            $this->errorResponse(
                "cannot update Authorization",
                HttpStatus::BAD_REQUEST->value,
                $validator->errors()
            )
        );
    }
}
