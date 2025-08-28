<?php

namespace App\Http\Requests\Api\V1\Rate;

use App\Enums\HttpStatus;
use App\Trait\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRateRequest extends FormRequest
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
            'rateable_id' => 'sometimes|numeric',
            'rateable_type' => 'sometimes|string|required_with:rateable_id',
            'rating' => 'required|numeric|min:0|max:5',
            'comment' => 'sometimes|nullable|string|max:400',
        ];
    }
    public function messages(): array
    {
        return [
            'rateable_id.numeric' => __('rate.rateable_id_numeric'),

            'rateable_type.required_with' => __('rate.rateable_type_required'),
            'rateable_type.string' => __('rate.rateable_type_string'),

            'rating.required' => __('rate.rating_required'),
            'rating.numeric' => __('rate.rating_numeric'),
            'rating.min' => __('rate.rating_min'),
            'rating.max' => __('rate.rating_max'),

            'comment.string' => __('rate.comment_string'),
            'comment.max' => __('rate.comment_max'),
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->errorResponse(
                _('validation.failed'),
                HttpStatus::UNPROCESSABLE_ENTITY->value,
                $validator->errors(),
            )
        );
    }
}
