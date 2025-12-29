<textarea 
    class="ckeditor" 
    name="{{ $fieldName }}" 
    id="{{ $id ?? $fieldName }}"
    data-placeholder="{{ $placeholder ?? 'ابدأ الكتابة هنا...' }}"
    data-min-height="{{ $minHeight ?? '700px' }}"
    {{ $attributes ?? '' }}
>{{ $value ?? '' }}</textarea>

