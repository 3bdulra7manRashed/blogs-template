<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Sanitize input data
        $this->merge([
            'name' => trim($this->name),
            'email' => trim(strtolower($this->email)),
            'message' => trim($this->message),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[\p{Arabic}\p{L}\s\-\.]+$/u', // Allow Arabic, Latin letters, spaces, hyphens, and dots
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns', // Strict email validation with DNS check
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', // Additional email pattern validation
            ],
            'message' => [
                'required',
                'string',
                'min:10',
                'max:2000',
            ],
            'g-recaptcha-response' => [
                'required',
                new \App\Rules\ReCaptcha(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            // Name validation messages
            'name.required' => 'الاسم الكامل مطلوب',
            'name.min' => 'الاسم يجب أن يحتوي على 3 أحرف على الأقل',
            'name.max' => 'الاسم يجب أن لا يتجاوز 100 حرف',
            'name.regex' => 'الاسم يجب أن يحتوي على حروف فقط',
            
            // Email validation messages
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح. يرجى التأكد من صحة العنوان',
            'email.max' => 'البريد الإلكتروني يجب أن لا يتجاوز 255 حرف',
            'email.regex' => 'صيغة البريد الإلكتروني غير صحيحة',
            
            // Message validation messages
            'message.required' => 'نص الرسالة مطلوب',
            'message.min' => 'الرسالة يجب أن تحتوي على 10 أحرف على الأقل',
            'message.max' => 'الرسالة يجب أن لا تتجاوز 2000 حرف',
            
            // reCAPTCHA validation message
            'g-recaptcha-response.required' => 'يرجى إكمال التحقق من reCAPTCHA',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'الاسم',
            'email' => 'البريد الإلكتروني',
            'message' => 'الرسالة',
        ];
    }

    /**
     * Get custom validation error messages for sanitized data
     */
    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check for suspicious content (spam prevention)
            if ($this->containsSuspiciousContent($this->message)) {
                $validator->errors()->add('message', 'الرسالة تحتوي على محتوى غير مقبول');
            }

            // Check for multiple exclamation marks or caps (spam indicators)
            if (substr_count($this->message, '!!!') > 0) {
                $validator->errors()->add('message', 'الرجاء كتابة رسالة مهنية');
            }

            // Check name doesn't contain numbers
            if (preg_match('/\d/', $this->name)) {
                $validator->errors()->add('name', 'الاسم لا يجب أن يحتوي على أرقام');
            }
        });
    }

    /**
     * Check if message contains suspicious content
     */
    private function containsSuspiciousContent(string $message): bool
    {
        $spamKeywords = [
            'click here',
            'buy now',
            'limited time',
            'make money',
            'viagra',
            'casino',
            'lottery',
            'prize',
            'winner',
        ];

        $lowerMessage = strtolower($message);
        foreach ($spamKeywords as $keyword) {
            if (stripos($lowerMessage, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }
}
