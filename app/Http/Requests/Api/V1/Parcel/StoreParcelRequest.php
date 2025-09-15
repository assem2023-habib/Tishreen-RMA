<?php

namespace App\Http\Requests\Api\V1\Parcel;

use App\Enums\HttpStatus;
use App\Trait\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;


class StoreParcelRequest extends FormRequest
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
            'route_id' => 'required|numeric|exists:branch_routes,id',
            'reciver_name' => 'required|string|max:250|min:2',
            'reciver_address' => 'required|string|max:500',
            'reciver_phone' => 'required|string|min:6|max:20|regex:/^\+?\d+$/',
            'weight' => 'required|numeric|min:0.1',
            'is_paid' => 'required|boolean',
        ];
    }
    public function messages()
    {
        return [

            'sender_type.required' => __('parcel.sender_type_required'),
            'sender_type.in' => __('parcel.sender_type_in'),

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

            'cost.numeric' => __('parcel.cost_numeric'),
            'cost.min' => __('parcel.cost_min'),

            'is_paid.required' => __('parcel.is_paid_required'),
            'is_paid.boolean' => __('parcel.is_paid_boolean'),

            'parcel_status.in' => __('parcel.parcel_status_in'),

            'tracking_number.unique' => __('parcel.tracking_number_unique'),
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
