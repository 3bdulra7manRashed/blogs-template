@extends('layouts.admin')

@php
use Illuminate\Support\Str;
@endphp

@section('title', 'المقالات')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-3xl font-serif font-bold text-brand-primary">المقالات</h1>
    <a href="{{ route('admin.posts.create') }}" class="flex items-center px-4 py-2 bg-brand-accent text-white hover:bg-amber-700 hover:text-white rounded-md hover:bg-opacity-90 transition-colors shadow-sm hover:shadow-md">
        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        مقال جديد
    </a>
</div>

<!-- Filters -->
<div class="mb-6 bg-white p-4 rounded-lg shadow">
    <form method="GET" action="{{ route('admin.posts.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث في المقالات..." class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-accent">
        <select name="status" class="py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-accent">
            <option value="">جميع الحالات</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>مسودة</option>
            <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>منشور</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-800 whitespace-nowrap">تصفية</button>
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.posts.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 text-center whitespace-nowrap">مسح</a>
        @endif
    </form>
</div>

<!-- Posts Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 md:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[200px]">العنوان</th>
                    <th class="px-4 md:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">الكاتب</th>
                    <th class="px-4 md:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">الحالة</th>
                    <th class="px-4 md:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">تاريخ النشر</th>
                    <th class="px-4 md:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($posts as $post)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 md:px-6 py-4 min-w-[200px]">
                            <!-- Mobile: Vertical Stack (flex-col, centered) -->
                            <div class="flex flex-col items-center text-center gap-y-2 md:block md:text-center">
                                <!-- Post Title -->
                                <a href="{{ route('post.show', $post->slug) }}" target="_blank" class="text-sm font-medium text-brand-primary hover:text-brand-accent transition-colors line-clamp-2">
                                    {{ $post->title }}
                                </a>
                                <!-- Author Name (Mobile only) -->
                                <div class="text-sm text-gray-900 md:hidden">{{ $post->author->name }}</div>
                                <!-- Status Badge (Mobile only) -->
                                <div class="md:hidden">
                                    @if($post->is_draft)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            مسودة
                                        </span>
                                    @elseif($post->isPublished())
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            منشور
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            مجدول
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center hidden md:table-cell">
                            {{ $post->author->name }}
                        </td>
                        <td class="px-4 md:px-6 py-4 whitespace-nowrap text-center hidden md:table-cell">
                            @if($post->is_draft)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    مسودة
                                </span>
                            @elseif($post->isPublished())
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    منشور
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    مجدول
                                </span>
                            @endif
                        </td>
                        <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center hidden md:table-cell">
                            {{ $post->published_at ? $post->published_at->format('Y/m/d') : '-' }}
                        </td>
                        <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            <div class="flex items-center gap-2 justify-center flex-wrap">
                                <a href="{{ route('admin.posts.edit', $post) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 hover:text-blue-800 font-medium text-xs transition-colors duration-200">
                                    <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    تعديل
                                </a>
                                <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="js-confirm inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 rounded-md hover:bg-red-100 hover:text-red-800 font-medium text-xs transition-colors duration-200"
                                            data-confirm-message="هل أنت متأكد من حذف هذا المقال؟">
                                        <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 md:px-6 py-8 text-center text-gray-500">
                            لا توجد مقالات
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if($posts->hasPages())
    <div class="mt-6" dir="ltr">
        {{ $posts->links() }}
    </div>
@endif
@endsection
