<?php

namespace App\Http\Requests\Api\V1\Parcel;

use App\Enums\HttpStatus;
use App\Trait\ApiResponse;
use Dotenv\Exception\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DeleteParcelRequest extends FormRequest
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
            'id' => 'required|numeric|exists:parcels,id',
        ];
    }
    public function validationData()
    {
        return array_merge($this->all(), [
            'id' => $this->route('parcel'),
        ]);
    }
    public function messages()
    {
        return [
            'parcel.id' => __('parcel.no_parcel_found'),
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->errorResponse(
                $validator->errors(),
                HttpStatus::NOT_FOUND->value,
                __('auth.validation_failed'),
            ),
        );
    }
}
