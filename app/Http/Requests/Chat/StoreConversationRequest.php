<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class StoreConversationRequest extends FormRequest
{
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
            'subject' => 'nullable|string|max:255',
            'related_id' => 'nullable|integer',
            'related_type' => 'nullable|string|in:parcel,branch,appointment', // يمكن إضافة المزيد من الأنواع هنا
        ];
    }

    public function messages()
    {
        return [
            'related_type.in' => 'النوع المرتبط غير مدعوم. الأنواع المدعومة هي: طرد، فرع، موعد.',
        ];
    }
}
