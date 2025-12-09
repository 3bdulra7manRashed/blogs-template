@extends('layouts.admin')

@section('title', 'إنشاء مقال جديد')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-3xl font-serif font-bold text-brand-primary">إنشاء مقال جديد</h1>
    <a href="{{ route('admin.posts.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors bg-white">
        العودة للقائمة
    </a>
</div>

<form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data" id="post-form">
    @csrf
    
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Main Column (Content) -->
        <div class="w-full lg:w-2/3 space-y-6">
            
            <!-- Title & Slug -->
            <div class="bg-white p-6 rounded shadow">
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">عنوان المقال</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" 
                           class="w-full px-4 py-3 text-xl font-bold border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-accent @error('title') border-red-500 @enderror"
                           placeholder="أدخل عنوان المقال هنا">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">الرابط الدائم (Slug)</label>
                    <div class="flex items-center">
                        <span class="text-gray-500 bg-gray-50 border border-l-0 border-gray-300 rounded-r-md px-3 py-2 text-sm" dir="ltr">{{ config('app.url') }}/post/</span>
                        <input type="text" name="slug" id="slug" value="{{ old('slug') }}" 
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-brand-accent @error('slug') border-red-500 @enderror text-sm"
                               dir="ltr">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">سيتم توليده تلقائيًا من العنوان إذا ترك فارغًا.</p>
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Content Editor -->
            <div class="bg-white p-6 rounded shadow">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">محتوى المقال</label>
                <div class="@error('content') border border-red-500 rounded @enderror">
                    @ckeditor('content')
                </div>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <!-- Sidebar Column (Metadata) -->
        <div class="w-full lg:w-1/3 space-y-6">
            
            <!-- Publishing Actions -->
            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">النشر</h3>
                
                <div class="mb-4">
                    <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">تاريخ النشر</label>
                    <input type="datetime-local" name="published_at" id="published_at" 
                           value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-accent text-sm"
                           dir="ltr">
                </div>

                <div class="flex items-center justify-between pt-4 border-t mt-4">
                    <button type="submit" name="is_draft" value="1" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors text-sm font-medium">
                        حفظ كمسودة
                    </button>
                    <button type="submit" name="is_draft" value="0" class="px-6 py-2 bg-brand-primary text-white rounded hover:bg-opacity-90 transition-colors text-sm font-medium shadow-sm">
                        نشر الآن
                    </button>
                </div>
            </div>

            <!-- Featured Image -->
            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">الصورة البارزة</h3>
                <div class="space-y-3">
                    <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-brand-accent transition-colors cursor-pointer" onclick="document.getElementById('featured_image').click()">
                        <div id="image-preview" class="hidden mb-2">
                            <img src="" alt="Preview" class="max-h-48 mx-auto rounded">
                        </div>
                        <div id="upload-placeholder">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <p class="mt-1 text-sm text-gray-600">انقر لرفع صورة</p>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF (Max 3MB)</p>
                        </div>
                    </div>
                    <input type="file" name="featured_image" id="featured_image" accept="image/*" class="hidden" onchange="previewImage(this)">
                    
                    <div>
                        <label for="featured_image_alt" class="block text-xs font-medium text-gray-700 mb-1 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-brand-accent flex-shrink-0"></span>
                            النص البديل
                        </label>
                        <input type="text" name="featured_image_alt" id="featured_image_alt" value="{{ old('featured_image_alt') }}" 
                               class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-brand-accent"
                               placeholder="وصف للصورة">
                        <p class="mt-1 text-xs text-gray-500">
                            لأفضل نتائج محركات البحث: اكتب وصفاً يعبر عن الصورة واربطه بموضوع المقال الرئيسي.
                        </p>
                    </div>
                    
                    @error('featured_image')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Categories -->
            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">الأقسام</h3>
                <div id="categories-list" class="max-h-60 overflow-y-auto pr-1 space-y-2 custom-scrollbar">
                    @foreach($categories as $category)
                        <label class="flex items-center space-x-2 space-x-reverse cursor-pointer hover:bg-gray-50 p-1 rounded">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}" 
                                   {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-brand-accent focus:ring-brand-accent h-4 w-4">
                            <span class="text-sm text-gray-700 select-none">{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="mt-3 pt-3 border-t">
                    <button type="button" onclick="openCategoryModal()" class="text-xs text-brand-accent hover:underline flex items-center font-medium">
                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        إضافة قسم جديد
                    </button>
                </div>
            </div>

            <!-- Tags -->
            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">الوسوم</h3>
                <div class="mb-2">
                    <select name="tags[]" id="tags" multiple class="w-full border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-accent">
                        @foreach($tags as $tag)
                            <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', [])) ? 'selected' : '' }}>
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">اضغط Ctrl (أو Cmd) لتحديد متعدد</p>
                </div>
                <div class="mt-3 pt-3 border-t">
                    <button type="button" onclick="openTagModal()" class="text-xs text-brand-accent hover:underline flex items-center font-medium">
                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        إضافة وسم جديد
                    </button>
                </div>
            </div>

        </div>
    </div>
</form>

@include('admin.posts.partials.category-quick-add-modal')
@include('admin.posts.partials.tag-quick-add-modal')
@endsection

@push('styles')
<style>
    /* Custom scrollbar for categories */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1; 
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #d1d5db; 
        border-radius: 2px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #9ca3af; 
    }
    
    /* Fade in animation for new categories */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }
</style>
@endpush

@push('scripts')
@ckeditorScripts

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Image Preview Script
        window.previewImage = function(input) {
            const previewBox = document.getElementById('image-preview');
            const previewImg = previewBox.querySelector('img');
            const placeholder = document.getElementById('upload-placeholder');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewBox.classList.remove('hidden');
                    if(placeholder) placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    
        // Auto-Slug Generator
        const titleInput = document.getElementById('title');
        const slugInput = document.getElementById('slug');
        
        if (titleInput && slugInput) {
            titleInput.addEventListener('blur', function() {
                if (slugInput.value.trim() === '') {
                    const title = this.value;
                    const slug = title.trim()
                        .replace(/\s+/g, '-')
                        .replace(/[^\w\u0600-\u06FF\-]+/g, '')
                        .replace(/\-\-+/g, '-')
                        .replace(/^-+/, '')
                        .replace(/-+$/, '');
                    
                    slugInput.value = slug;
                }
            });
            
            // Force generation on form submit (before sending to server)
            const form = document.getElementById('post-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (!slugInput.value.trim() && titleInput.value.trim()) {
                        // Generate slug instantly before sending
                        let slug = titleInput.value.trim()
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

