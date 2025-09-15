<?php

namespace App\Http\Requests\Api\V1\Parcel;

use App\Enums\HttpStatus;
use App\Trait\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateParcelRequest extends FormRequest
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
            'sender_id' => 'sometimes|numeric|exists:users,id',
            'route_id' => 'sometimes|numeric|exists:branch_routes,id',
            'reciver_name' => 'sometimes|string|min:2|max:255',
            'reciver_address' => 'sometimes|string|min:2|max:255',
            'reciver_phone' => 'sometimes|string|min:6|max:20|regex:/^\+?\d+$',
            'weight' => 'sometimes|numeric|min:0.1',
            'price_policy_id' => 'sometimes|numeric|exists:pricing_policies,id',
        ];
    }
    public function messages()
    {
        return [
            'sender_id.required' => __('parcel.sender_id_required'),
            'sender_id.numeric' => __('parcel.sender_id_numeric'),
            'sender_id.exists' => __('parcel.sender_id_exists'),

            'route_id.required' => __('parcel.route_id_required'),
            'route_id.numeric' => __('parcel.route_id_numeric'),
            'route_id.exists' => __('parcel.route_id_exists'),

            'reciver_name.required' => __('parcel.reciver_name_required'),
            'reciver_name.string' => __('parcel.reciver_name_string'),
            'reciver_name.max' => __('parcel.reciver_name_max'),
            'reciver_name.min' => __('parcel.reciver_name_min'),

            'reciver_address.string' => __('parcel.reciver_address_string'),
            'reciver_address.max' => __('parcel.reciver_address_max'),

            'reciver_phone.required' => __('parcel.reciver_phone_required'),
            'reciver_phone.string' => __('parcel.reciver_phone_string'),
            'reciver_phone.min' => __('parcel.reciver_phone_min'),
            'reciver_phone.max' => __('parcel.reciver_phone_max'),
            'reciver_phone.regex' => __('parcel.reciver_phone_regex'),

            'weight.required' => __('parcel.weight_required'),
            'weight.numeric' => __('parcel.weight_numeric'),
            'weight.min' => __('parcel.weight_min'),

            'price_policy_id.required' => __('parcel.price_policy_id_required'),
            'price_policy_id.numeric' => __('parcel.price_policy_id_numeric'),
            'price_policy_id.exists' => __('parcel.price_policy_id_exists'),
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->errorResponse(
                $validator->errors(),
                HttpStatus::UNPROCESSABLE_ENTITY->value,
                __('auth.validation_failed'),
            )
        );
    }
}
