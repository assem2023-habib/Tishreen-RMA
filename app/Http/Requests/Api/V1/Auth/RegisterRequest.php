<?php

namespace App\Http\Requests\Api\V1\Auth;

use App\Enums\HttpStatus;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
        return [
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email',
            'password'          => 'required|string|min:8|confirmed',
            'phone'             => 'required|unique:users',
            'birthday'          => 'required',
            'city_id'           => 'required|exists:cities,id',
            'national_number'   => 'required|digits:11|unique:users,national_number',


        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => __('auth.first_name_required'),
            'last_name.required' => __('auth.last_name_required'),
            'email.required' => __('auth.email_required'),
            'email.email' => __('auth.email_email'),
            'email.unique' => __('auth.email_unique'),
            'password.required' => __('auth.password_required'),
            'password.confirmed' => __('auth.password_confirmed'),
            'phone.required' => __('auth.phone_required'),
            'city_id.required' => __('auth.city_id_required'),
            'city_id.exists' => __('auth.city_id_exists'),
            'national_number.required' => __('auth.national_number_required'),
            'national_number.unique' => __('auth.national_number_unique'),


        ];
    }
}
