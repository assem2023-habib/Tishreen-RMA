<?php

namespace App\Http\Requests\Api\V1\Users;

use App\Enums\HttpStatus;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => __('auth.validation_failed'),
                'errors' => $validator->errors(),
            ], HttpStatus::UNPROCESSABLE_ENTITY->value,)
        );
    }

    public function rules(): array
    {
        $userId = Auth::id();

        return [
            'first_name'        => 'sometimes|string|max:255',
            'last_name'         => 'sometimes|string|max:255',
            'email'             => 'sometimes|email|unique:users,email,' . $userId,
            'user_name'         => 'sometimes|string|max:255|unique:users,user_name,' . $userId,
            'phone'             => 'sometimes|unique:users,phone,' . $userId,
            'birthday'          => 'sometimes|date',
            'city_id'           => 'sometimes|exists:cities,id',
            'national_number'   => 'sometimes|digits:11|unique:users,national_number,' . $userId,
            'address'           => 'sometimes|string|max:500',
            'image_profile'     => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
