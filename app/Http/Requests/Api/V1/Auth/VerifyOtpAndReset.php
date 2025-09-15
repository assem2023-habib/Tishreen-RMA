<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class VerifyOtpAndReset extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'     => 'required|email|exists:users,email',
            'otp_code'  => 'required|digits:6',
            'new_password'  => 'required|string|min:8|regex:/^[A-Za-z0-9@#$%^&*!]+$/',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'     => __('auth.email_required'),
            'email.email'        => __('auth.email_email'),
            'email.exists'       => __('auth.email_not_found'),

            'otp_code.required'  => __('auth.otp_required'),
            'otp_code.digits'    => __('auth.otp_digits'),

            'new_password.required'  => __('auth.password_required'),
            'new_password.min'       => __('auth.password_min'),
            'new_password.regex'     => __('auth.password_regex'),
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'message' => __('auth.validation_failed'),
            'errors'  => $validator->errors(),
        ], 422));
    }
}
