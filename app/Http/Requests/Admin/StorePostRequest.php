<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Convert button value to boolean
        // Button sends "1" for draft, "0" for publish
        $this->merge([
            'is_draft' => $this->input('is_draft', '0') === '1',
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:200', 'unique:posts,slug'],
            'content' => ['required', 'string'],
            'featured_image' => ['nullable', 'image', 'max:3072'],
            'featured_image_alt' => ['nullable', 'string', 'max:255'],
            'is_draft' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
            'meta' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان المقال مطلوب',
            'title.max' => 'عنوان المقال يجب أن لا يتجاوز 180 حرف',
            'slug.max' => 'الرابط الدائم يجب أن لا يتجاوز 200 حرف',
            'slug.unique' => 'الرابط الدائم مستخدم مسبقاً',
            'content.required' => 'محتوى المقال مطلوب',
            'featured_image.image' => 'يجب أن يكون الملف صورة',
            'featured_image.max' => 'حجم الصورة يجب أن لا يتجاوز 3 ميجابايت',
            'categories.*.exists' => 'القسم المحدد غير موجود',
            'tags.*.exists' => 'الوسم المحدد غير موجود',
        ];
    }
}

