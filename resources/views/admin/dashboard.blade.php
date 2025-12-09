@extends('layouts.admin')

@section('title', 'لوحة التحكم')

@section('content')
<div class="space-y-6">
    <!-- Page Header with Action Button -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-brand-primary">لوحة التحكم</h1>
            <p class="text-gray-500 mt-1 text-sm md:text-base">إليك نظرة عامة على نشاط المدونة</p>
        </div>
        <a href="{{ route('admin.posts.create') }}" class="flex items-center px-4 md:px-6 py-2 md:py-3 bg-brand-accent text-white rounded-lg shadow-md  hover:bg-amber-700 hover:text-white transition-all duration-200 hover:shadow-lg whitespace-nowrap">
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span class="font-semibold">مقال جديد</span>
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Posts Card -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-gray-500 text-sm mb-1">إجمالي المقالات</p>
                    <p class="text-3xl font-bold text-brand-primary">{{ $totalPosts }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        <span class="text-green-600">{{ $totalPublished }}</span> منشور، 
                        <span class="text-gray-600">{{ $totalDrafts }}</span> مسودة
                    </p>
                </div>
                <div class="bg-blue-50 p-3 rounded-full">
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.posts.index') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                عرض جميع المقالات ←
            </a>
        </div>

        <!-- Total Users Card -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-gray-500 text-sm mb-1">المستخدمين</p>
                    <p class="text-3xl font-bold text-brand-primary">{{ $totalUsers }}</p>
                    <p class="text-xs text-gray-400 mt-1">إجمالي المستخدمين</p>
                </div>
                <div class="bg-green-50 p-3 rounded-full">
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
            @role('admin')
                <a href="{{ route('admin.users.index') }}" class="text-xs text-green-600 hover:text-green-800 font-medium">
                    إدارة المستخدمين ←
                </a>
            @endrole
        </div>

        <!-- Categories Card -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-gray-500 text-sm mb-1">الأقسام</p>
                    <p class="text-3xl font-bold text-brand-primary">{{ $totalCategories }}</p>
                    <p class="text-xs text-gray-400 mt-1">إجمالي الأقسام</p>
                </div>
                <div class="bg-purple-50 p-3 rounded-full">
                    <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.categories.index') }}" class="text-xs text-purple-600 hover:text-purple-800 font-medium">
                إدارة الأقسام ←
            </a>
        </div>

        <!-- Total Likes Card -->
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-gray-500 text-sm mb-1">الإعجابات</p>
                    <p class="text-3xl font-bold text-brand-primary">{{ $totalLikes }}</p>
                    <p class="text-xs text-gray-400 mt-1">إجمالي الإعجابات</p>
                </div>
                <div class="bg-red-50 p-3 rounded-full">
                    <svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </div>
            </div>
            <div class="text-xs text-gray-400">
                من {{ $totalPublished }} مقال منشور
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- Publishing Activity Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-brand-primary mb-4">نشاط النشر</h2>
            <div class="h-64">
                <canvas id="postsChart"></canvas>
            </div>
        </div>

        <!-- Popular Posts -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-brand-primary mb-4">المقالات الأكثر إعجاباً</h2>
            <div class="space-y-3">
                @forelse($popularPosts as $post)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-1">
                            <a href="{{ route('admin.posts.edit', $post) }}" class="text-sm font-medium text-brand-primary hover:text-brand-accent line-clamp-1">
                                {{ $post->title }}
                            </a>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $post->published_at?->diffForHumans() ?? 'مسودة' }}
                            </p>
                        </div>
                        <div class="flex items-center mr-3">
                            <svg class="w-4 h-4 text-red-500 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            <span class="text-sm font-bold text-gray-700">{{ $post->likes_count }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-8">لا توجد مقالات حالياً</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Posts Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-brand-primary">أحدث المقالات</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">الكاتب</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">الحالة</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">التاريخ</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentPosts as $post)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <!-- Mobile: Vertical Stack (flex-col, centered) -->
                                <div class="flex flex-col items-center text-center gap-y-2 md:block md:text-center">
                                    <!-- Post Title -->
                                    <div class="text-sm font-medium text-brand-primary">{{ $post->title }}</div>
                                    <!-- Author Name (Mobile only) -->
                                    <div class="text-sm text-gray-900 md:hidden">{{ $post->author->name }}</div>
                                    <!-- Status Badge (Mobile only) -->
                                    <div class="md:hidden">
                                        @if($post->is_draft)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                مسودة
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                منشور
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell text-center">
                                <div class="text-sm text-gray-900">{{ $post->author->name }}</div>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell text-center">
                                @if($post->is_draft)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        مسودة
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        منشور
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 hidden md:table-cell text-center">
                                {{ $post->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-center">
                                <div class="flex items-center gap-2 justify-center">
                                    <a href="{{ route('post.show', $post->slug) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 rounded-md hover:bg-green-100 hover:text-green-800 font-medium text-xs transition-colors duration-200">
                                        <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        عرض
                                    </a>
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 hover:text-blue-800 font-medium text-xs transition-colors duration-200">
                                        <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        تعديل
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                لا توجد مقالات حالياً
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Posts Per Month Chart
    const ctx = document.getElementById('postsChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartData['labels']),
            datasets: [{
                label: 'عدد المقالات',
                data: @json($chartData['data']),
                borderColor: '#c37c54',
                backgroundColor: 'rgba(195, 124, 84, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#c37c54',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    rtl: true,
                    labels: {
                        font: {
                            family: 'Cairo, sans-serif',
                            size: 12
                        },
                        usePointStyle: true,
                        padding: 15
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: {
                        family: 'Cairo, sans-serif',
                        size: 13
                    },
                    bodyFont: {
                        family: 'Cairo, sans-serif',
                        size: 12
                    },
                    padding: 12,
                    cornerRadius: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        font: {
                            family: 'Cairo, sans-serif'
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    ticks: {
                        font: {
                            family: 'Cairo, sans-serif'
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection

