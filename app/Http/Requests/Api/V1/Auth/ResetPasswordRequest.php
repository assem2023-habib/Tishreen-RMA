<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'                 => 'required|email|exists:users,email',
            'token'                 => 'required',
            'password'              => 'required|string|min:8|regex:/^[A-Za-z0-9@#$%^&*!]+$/',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('auth.email_required'),
            'email.email' => __('auth.email_email'),
            'email.exists' => __('auth.email_not_found'),
            'token.required' => __('auth.token_required'),
            'password.required' => __('auth.password_required'),
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => __('auth.validation_failed'),
            'errors' => $validator->errors(),
        ], 422));
    }
}
