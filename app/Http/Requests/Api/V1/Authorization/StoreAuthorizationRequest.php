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
        ];
    }
    public function messages()
    {
        return [];
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
