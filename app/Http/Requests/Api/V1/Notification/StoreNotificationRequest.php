<?php

namespace App\Http\Requests\Api\V1\Notification;

use App\Trait\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreNotificationRequest extends FormRequest
{
    use ApiResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'notification_type' => 'required|string',
            'notification_priority' => 'required|in:Important,Reminder,General',
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id',
        ];
    }

    /**
     * رسائل مخصصة للتحقق
     */
    public function messages(): array
    {
        return [
            'title.required' => __('notifications.title_required'),
            'title.string'   => __('notifications.title_string'),
            'title.max'      => __('notifications.title_max'),

            'message.required' => __('notifications.message_required'),
            'message.string'   => __('notifications.message_string'),

            'notification_type.required' => __('notifications.type_required'),
            'notification_type.string'   => __('notifications.type_string'),

            'notification_priority.required' => __('notifications.priority_required'),
            'notification_priority.in'       => __('notifications.priority_in'),

            'user_ids.array' => __('notifications.user_ids_array'),
            'user_ids.*.exists' => __('notifications.user_ids_exists'),
        ];
    }

    /**
     * عند فشل التحقق نرجّع استجابة موحّدة
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->errorResponse(
                __('validation.failed'),
                422,
                $validator->errors()
            )
        );
    }
}
