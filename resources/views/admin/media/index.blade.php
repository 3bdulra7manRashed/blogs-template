@extends('layouts.admin')

@php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
@endphp

@section('title', 'الوسائط')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl md:text-3xl font-serif font-bold text-brand-primary mb-4">مكتبة الوسائط</h1>
    
    <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        @csrf
        <div class="flex-1">
            <input type="file" name="file" accept=".jpeg,.jpg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar" required 
                   class="block w-full text-sm text-gray-900 border rounded-lg cursor-pointer bg-gray-50 focus:outline-none 
                          {{ $errors->has('file') ? 'border-red-500' : 'border-gray-300' }}
                          file:mr-0 file:py-2 file:px-4 
                          file:rounded-lg file:border-0 
                          file:text-sm file:font-semibold 
                          file:bg-brand-accent file:text-white 
                          hover:file:bg-amber-700 file:cursor-pointer">
            @error('file')
                <p class="text-red-500 text-sm mt-1" role="alert">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="flex items-center justify-center px-4 py-2 bg-brand-accent text-white rounded-lg hover:bg-amber-700 transition-colors shadow-sm hover:shadow-md whitespace-nowrap">
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
            </svg>
            رفع ملف
        </button>
    </form>
</div>

@if(request()->has('search') || $media->count() > 0)
    <div class="mb-4">
        <form method="GET" action="{{ route('admin.media.index') }}" class="flex items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث باسم الملف..." class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-accent text-sm">
            <button type="submit" class="px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-800 text-sm">بحث</button>
            @if(request()->has('search'))
                <a href="{{ route('admin.media.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 text-sm">مسح</a>
            @endif
        </form>
    </div>
@endif

<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
    @forelse($media as $item)
        @php
            $cleanPath = str_replace('public/', '', $item->path);
            $imageUrl = asset('storage/' . $cleanPath);
            $fallbackUrl = 'https://via.placeholder.com/400x400/e5e7eb/6b7280?text=No+Image';
        @endphp
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-300 group flex flex-col">
            <div class="aspect-square bg-gray-50 relative overflow-hidden border-b border-gray-100 group-hover:border-gray-200">
                <img src="{{ $imageUrl }}" 
                     alt="{{ $item->alt_text ?? $item->filename }}" 
                     onerror="this.onerror=null; this.src='{{ $fallbackUrl }}';"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 cursor-pointer"
                     onclick="window.open('{{ $imageUrl }}', '_blank')">
            </div>

            <div class="p-3 flex flex-col gap-3 flex-1">
                <div class="flex items-start justify-between gap-2">
                    <p class="text-sm font-medium text-gray-700 truncate flex-1" title="{{ $item->filename }}">
                        {{ $item->filename }}
                    </p>
                    <span class="shrink-0 text-[10px] font-bold text-gray-500 bg-gray-100 border border-gray-200 px-1.5 py-0.5 rounded">
                        {{ number_format($item->size / 1024, 1) }} KB
                    </span>
                </div>

                <div class="flex items-center gap-2 mt-auto">
                    <button type="button" 
                            onclick="copyToClipboard('{{ $imageUrl }}', this)"
                            class="flex-1 flex items-center justify-center gap-1.5 bg-gray-50 hover:bg-brand-accent hover:text-white text-gray-600 border border-gray-200 py-1.5 px-2 rounded transition-colors duration-200 text-xs font-medium group/btn shadow-sm">
                        <span class="icon-placeholder">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                        </span>
                        <span class="btn-text">نسخ</span>
                    </button>

                    <form id="delete-form-{{ $item->id }}" action="{{ route('admin.media.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirmDelete(this, 'هل أنت متأكد من حذف هذا الملف نهائياً؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 text-red-600 hover:text-white hover:bg-red-500 rounded border border-red-100 hover:border-red-500 transition-colors shadow-sm" title="حذف الملف">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full flex flex-col items-center justify-center py-16 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
            <svg class="h-16 w-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <p class="text-gray-500 font-medium">لا توجد ملفات وسائط حالياً</p>
            <p class="text-gray-400 text-sm mt-1">قم برفع ملفات جديدة لتظهر هنا</p>
        </div>
    @endforelse
</div>

@if($media->hasPages())
    <div class="mt-8" dir="ltr">
        {{ $media->links() }}
    </div>
@endif

{{-- سكربت النسخ للحافظة --}}
<script>
    function copyToClipboard(url, btn) {
        navigator.clipboard.writeText(url).then(() => {
            const originalIcon = btn.querySelector('.icon-placeholder').innerHTML;
            const originalText = btn.querySelector('.btn-text').innerText;
            
            btn.classList.remove('bg-gray-50', 'text-gray-600', 'border-gray-200', 'hover:bg-brand-accent', 'hover:text-white');
            btn.classList.add('bg-green-50', 'text-green-600', 'border-green-200');
            
            btn.querySelector('.icon-placeholder').innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            `;
            btn.querySelector('.btn-text').innerText = 'تم النسخ!';

            setTimeout(() => {
                btn.classList.remove('bg-green-50', 'text-green-600', 'border-green-200');
                btn.classList.add('bg-gray-50', 'text-gray-600', 'border-gray-200', 'hover:bg-brand-accent', 'hover:text-white');
                
                btn.querySelector('.icon-placeholder').innerHTML = originalIcon;
                btn.querySelector('.btn-text').innerText = originalText;
            }, 2000);
        }).catch(err => {
            console.error('فشل النسخ', err);
            alert('حدث خطأ أثناء نسخ الرابط');
        });
    }
</script>
@endsection