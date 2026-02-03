<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
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
            'content' => 'required_without:attachment|string',
            'type' => 'nullable|in:text,image,file',
            'attachment' => 'nullable|file|max:10240', // 10MB max
            'attachment_url' => 'nullable|string', // في حال إرسال الرابط مباشرة
        ];
    }

    public function messages()
    {
        return [
            'content.required_without' => 'يجب إدخال نص الرسالة أو إرفاق ملف.',
            'attachment.max' => 'حجم الملف يجب ألا يتجاوز 10 ميجابايت.',
        ];
    }
}
