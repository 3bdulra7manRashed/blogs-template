@extends('layouts.admin')

@section('title', 'تحرير وسم')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-3xl font-serif font-bold text-brand-primary">تحرير وسم: {{ $tag->name }}</h1>
    <a href="{{ route('admin.tags.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors bg-white">
        العودة للقائمة
    </a>
</div>

<form action="{{ route('admin.tags.update', $tag) }}" method="POST" id="tag-form">
    @csrf
    @method('PUT')
    
    <div class="flex flex-col lg:flex-row gap-6">
        <div class="w-full lg:w-2/3 space-y-6">
            
            <div class="bg-white p-6 rounded shadow">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">اسم الوسم</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $tag->name) }}" 
                           class="w-full px-4 py-3 text-xl font-bold border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-accent @error('name') border-red-500 @enderror"
                           placeholder="أدخل اسم الوسم هنا">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">الرابط الدائم (Slug)</label>
                    <div class="flex items-center">
                        <span class="text-gray-500 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md px-3 py-2 text-sm" dir="ltr">{{ config('app.url') }}/tag/</span>
                        <input type="text" name="slug" id="slug" value="{{ old('slug', $tag->slug) }}" 
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-brand-accent @error('slug') border-red-500 @enderror text-sm"
                               dir="ltr">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">سيتم توليده تلقائيًا من الاسم إذا ترك فارغًا.</p>
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-sm font-medium text-gray-700 mb-3">معاينة الوسم</h3>
                <div class="flex items-center">
                    <span id="tag-preview" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-brand-accent text-white">
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                        </svg>
                        <span id="preview-text">{{ $tag->name }}</span>
                    </span>
                </div>
                <p class="mt-2 text-xs text-gray-500">هكذا سيظهر الوسم في الموقع</p>
            </div>

        </div> 
        </form>

        <div class="w-full lg:w-1/3 space-y-6">
            
            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">الإجراءات</h3>

                <div class="flex items-center justify-between pt-4">
                    <a href="{{ route('admin.tags.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors text-sm font-medium">
                        إلغاء
                    </a>
                    <button type="submit" form="tag-form" class="px-6 py-2 bg-brand-primary text-white rounded hover:bg-opacity-90 transition-colors text-sm font-medium shadow-sm">
                        تحديث الوسم
                    </button>
                </div>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold text-gray-800 mb-3 text-sm">إحصائيات</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-xs text-gray-600">عدد المقالات</span>
                        <span class="text-sm font-bold text-brand-accent">{{ $tag->posts_count ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-xs text-gray-600">تاريخ الإنشاء</span>
                        <span class="text-xs text-gray-500">{{ $tag->created_at->format('Y-m-d') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-purple-50 border border-purple-200 p-4 rounded shadow-sm">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-purple-600 ml-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="text-sm text-purple-800">
                        <p class="font-semibold mb-1">ماهي الوسوم؟</p>
                        <p class="text-xs">الوسوم هي كلمات مفتاحية تساعد القراء في العثور على محتوى مشابه. استخدم وسومًا محددة وواضحة.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold text-gray-800 mb-3 text-sm">نصائح سريعة</h3>
                <ul class="space-y-2 text-xs text-gray-600">
                    <li class="flex items-start">
                        <span class="text-brand-accent ml-2">•</span>
                        <span>استخدم وسومًا قصيرة ومحددة</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-brand-accent ml-2">•</span>
                        <span>تجنب التكرار مع الأقسام الموجودة</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-brand-accent ml-2">•</span>
                        <span>يمكن استخدام الوسم في عدة مقالات</span>
                    </li>
                </ul>
            </div>

            <div class="bg-red-50 border border-red-200 p-4 rounded shadow-sm">
                <h3 class="font-bold text-red-800 mb-2 text-sm">منطقة الخطر</h3>
                <p class="text-xs text-red-700 mb-3">حذف هذا الوسم سيزيله من جميع المقالات المرتبطة به.</p>
                <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="js-confirm w-full px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors text-xs font-medium"
                            data-confirm-message="هل أنت متأكد من حذف هذا الوسم؟ سيتم إزالة الوسم من جميع المقالات المرتبطة به.">
                        حذف الوسم
                    </button>
                </form>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
<script>
    // Auto-Slug Generator (Arabic Friendly)
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    const previewText = document.getElementById('preview-text');
    
    // Update preview in real-time
    nameInput.addEventListener('input', function() {
        const name = this.value || '{{ $tag->name }}';
        previewText.textContent = name;
    });
    
    nameInput.addEventListener('blur', function() {
        // Only generate if slug is empty
        if (slugInput.value.trim() === '') {
            const name = this.value;
            const slug = name.trim()
                .replace(/\s+/g, '-')           // Replace spaces with -
                .replace(/[^\w\u0600-\u06FF\-]+/g, '') // Remove non-word chars (preserving Arabic & -)
                .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                .replace(/^-+/, '')             // Trim - from start
                .replace(/-+$/, '');            // Trim - from end
            
            slugInput.value = slug;
        }
    });
    
    // Force generation on form submit (before sending to server)
    const form = document.getElementById('tag-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!slugInput.value.trim() && nameInput.value.trim()) {
                // Generate slug instantly before sending
                let slug = nameInput.value.trim()
                    .replace(/\s+/g, '-')           // Replace spaces with -
                    .replace(/[^\w\u0600-\u06FF\-]+/g, '') // Keep Arabic & English chars & numbers
                    .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                    .replace(/^-+/, '')             // Trim - from start
                    .replace(/-+$/, '');            // Trim - from end
                
                slugInput.value = slug;
            }
        });
    }
</script>
@endpush