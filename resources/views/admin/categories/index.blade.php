@extends('layouts.admin')

@section('title', 'الأقسام')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-3xl font-serif font-bold text-brand-primary">الأقسام</h1>
    <a href="{{ route('admin.categories.create') }}" class="flex items-center px-4 py-2 bg-brand-accent text-white  hover:bg-amber-700 hover:text-white rounded-md hover:bg-opacity-90 transition-colors shadow-sm hover:shadow-md">
        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        إضافة قسم جديد
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 md:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
                    <th class="px-4 md:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">الرابط</th>
                    <th class="px-4 md:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">عدد المقالات</th>
                    <th class="px-4 md:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($categories as $category)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 md:px-6 py-4 text-sm font-medium text-gray-900 text-center">
                            <div>{{ $category->name }}</div>
                            <div class="md:hidden text-xs text-gray-500 mt-1 font-mono">{{ $category->slug }}</div>
                        </td>
                        <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center font-mono hidden md:table-cell">
                            {{ $category->slug }}
                        </td>
                        <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center hidden md:table-cell">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $category->posts_count }}
                            </span>
                        </td>
                        <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            <div class="flex items-center gap-2 justify-center flex-wrap">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 hover:text-blue-800 font-medium text-xs transition-colors duration-200">
                                    <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    تعديل
                                </a>
                                @can('delete', $category)
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا القسم؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 rounded-md hover:bg-red-100 hover:text-red-800 font-medium text-xs transition-colors duration-200">
                                            <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            حذف
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 md:px-6 py-8 text-center text-gray-500">
                            لا توجد أقسام حالياً
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

