<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ConfirmEmailOtpAndVerifyEmailRequest extends FormRequest
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
        ];
    }

    public function messages(): array
    {
        return [


            'otp_code.required'  => __('auth.otp_required'),
            'otp_code.digits'    => __('auth.otp_digits'),

            'password.required'  => __('auth.password_required'),
            'password.min'       => __('auth.password_min'),
            'password.regex'     => __('auth.password_regex'),
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
