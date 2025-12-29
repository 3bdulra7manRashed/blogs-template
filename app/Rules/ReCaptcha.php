<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class ReCaptcha implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Skip validation if reCAPTCHA is not configured
        if (empty(config('services.recaptcha.secret_key'))) {
            return;
        }

        // Check if value is provided
        if (empty($value)) {
            $fail('يرجى إكمال التحقق من reCAPTCHA');
            return;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        if (!$response->successful()) {
            $fail('فشل التحقق من reCAPTCHA. يرجى المحاولة مرة أخرى.');
            return;
        }

        $result = $response->json();

        // Check if verification was successful
        if (!($result['success'] ?? false)) {
            $errorCodes = $result['error-codes'] ?? [];
            
            // Provide specific error messages
            if (in_array('timeout-or-duplicate', $errorCodes)) {
                $fail('انتهت صلاحية التحقق. يرجى المحاولة مرة أخرى.');
            } elseif (in_array('invalid-input-response', $errorCodes)) {
                $fail('التحقق غير صحيح. يرجى المحاولة مرة أخرى.');
            } else {
                $fail('فشل التحقق من reCAPTCHA. يرجى المحاولة مرة أخرى.');
            }
            return;
        }

        // For reCAPTCHA v3, check score (v2 doesn't have score)
        if (isset($result['score'])) {
            $score = $result['score'];
            $threshold = config('services.recaptcha.threshold', 0.5);

            if ($score < $threshold) {
                $fail('فشل التحقق الأمني. يرجى المحاولة مرة أخرى.');
            }
        }
    }
}

