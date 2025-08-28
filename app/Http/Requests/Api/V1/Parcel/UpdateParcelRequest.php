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
            '' => '',
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
