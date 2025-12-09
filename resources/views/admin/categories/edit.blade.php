@extends('layouts.admin')

@section('title', 'تحرير قسم')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-3xl font-serif font-bold text-brand-primary">تحرير قسم: {{ $category->name }}</h1>
    <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors bg-white">
        العودة للقائمة
    </a>
</div>

<form action="{{ route('admin.categories.update', $category) }}" method="POST" id="category-form">
    @csrf
    @method('PUT')
    
    <div class="flex flex-col lg:flex-row gap-6">
        <div class="w-full lg:w-2/3 space-y-6">
            
            <div class="bg-white p-6 rounded shadow">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">اسم القسم</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" 
                           class="w-full px-4 py-3 text-xl font-bold border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-accent @error('name') border-red-500 @enderror"
                           placeholder="أدخل اسم القسم هنا">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">الرابط الدائم (Slug)</label>
                    <div class="flex items-center">
                        <span class="text-gray-500 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md px-3 py-2 text-sm" dir="ltr">{{ config('app.url') }}/category/</span>
                        <input type="text" name="slug" id="slug" value="{{ old('slug', $category->slug) }}" 
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
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                <textarea 
                    class="ckeditor" 
                    name="description" 
                    id="description"
                    data-placeholder="أدخل وصف القسم هنا..."
                    data-min-height="350px"
                >{{ old('description', $category->description ?? '') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

        </div>
        </form>

        <div class="w-full lg:w-1/3 space-y-6">
            
            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">الإعدادات</h3>
                
                <div class="mb-4">
                    <label for="order_column" class="block text-sm font-medium text-gray-700 mb-2">ترتيب العرض</label>
                    <input type="number" name="order_column" id="order_column" 
                           value="{{ old('order_column', $category->order_column) }}" 
                           form="category-form"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-accent text-sm"
                           min="0">
                    <p class="mt-1 text-xs text-gray-500">الأقسام ذات الرقم الأصغر تظهر أولاً</p>
                </div>

                <div class="flex items-center justify-between pt-4 border-t mt-4">
                    <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors text-sm font-medium">
                        إلغاء
                    </a>
                    <button type="submit" form="category-form" class="px-6 py-2 bg-brand-primary text-white rounded hover:bg-opacity-90 transition-colors text-sm font-medium shadow-sm">
                        تحديث القسم
                    </button>
                </div>
            </div>

            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold text-gray-800 mb-3 text-sm">إحصائيات</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-xs text-gray-600">عدد المقالات</span>
                        <span class="text-sm font-bold text-brand-accent">{{ $category->posts_count ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-xs text-gray-600">تاريخ الإنشاء</span>
                        <span class="text-xs text-gray-500">{{ $category->created_at->format('Y-m-d') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 p-4 rounded shadow-sm">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 ml-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">ماهي الأقسام؟</p>
                        <p class="text-xs">الأقسام تساعد في تنظيم المقالات حسب المواضيع الرئيسية. يمكن لكل مقال أن ينتمي لعدة أقسام.</p>
                    </div>
                </div>
            </div>

            <div class="bg-red-50 border border-red-200 p-4 rounded shadow-sm">
                <h3 class="font-bold text-red-800 mb-2 text-sm">منطقة الخطر</h3>
                <p class="text-xs text-red-700 mb-3">حذف هذا القسم سيؤثر على جميع المقالات المرتبطة به.</p>
                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="js-confirm w-full px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors text-xs font-medium"
                            data-confirm-message="هل أنت متأكد من حذف هذا القسم؟ سيتم إزالة القسم من جميع المقالات المرتبطة به.">
                        حذف القسم
                    </button>
                </form>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
@ckeditorScripts

<script>
    // Auto-Slug Generator (Arabic Friendly)
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');
        
        if (nameInput && slugInput) {
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
            const form = document.getElementById('category-form');
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
        }
    });
</script>
@endpush