<div class="p-5" dir="rtl" x-data="{ activeTab: 'recent' }">
    
    <!-- Simple Tabs -->
    <div class="flex gap-3 mb-5 pb-3 border-b border-gray-200">
        <button 
            @click="activeTab = 'recent'" 
            :class="activeTab === 'recent' ? 'text-brand-accent font-bold' : 'text-gray-500'"
            class="text-sm transition-colors hover:text-brand-accent">
            الحديثة
        </button>
        <button 
            @click="activeTab = 'liked'" 
            :class="activeTab === 'liked' ? 'text-brand-accent font-bold' : 'text-gray-500'"
            class="text-sm transition-colors hover:text-brand-accent">
            الأكثر إعجاباً
        </button>
        <button 
            @click="activeTab === 'read'" 
            :class="activeTab === 'read' ? 'text-brand-accent font-bold' : 'text-gray-500'"
            class="text-sm transition-colors hover:text-brand-accent">
            الأكثر قراءة
        </button>
    </div>

    <!-- Recent Posts -->
    <div x-show="activeTab === 'recent'" class="space-y-3">
        @forelse($trendingRecentPosts as $post)
            <a href="{{ route('post.show', $post->slug) }}" class="block group">
                <h4 class="text-sm font-bold text-gray-900 group-hover:text-brand-accent transition-colors line-clamp-2 mb-1">
                    {{ $post->title }}
                </h4>
                <p class="text-xs text-gray-500">
                    {{ $post->published_at->format('Y/m/d') }}
                </p>
            </a>
            @if(!$loop->last)
                <div class="border-b border-gray-100"></div>
            @endif
        @empty
            <p class="text-xs text-gray-400 text-center py-4">لا توجد مقالات</p>
        @endforelse
    </div>

    <!-- Most Liked Posts -->
    <div x-show="activeTab === 'liked'" class="space-y-3">
        @forelse($trendingMostLikedPosts as $post)
            <a href="{{ route('post.show', $post->slug) }}" class="block group">
                <h4 class="text-sm font-bold text-gray-900 group-hover:text-brand-accent transition-colors line-clamp-2 mb-1">
                    {{ $post->title }}
                </h4>
                <div class="flex items-center gap-1 text-xs text-gray-500">
                    <svg class="w-3.5 h-3.5 text-red-500 fill-current" viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                    <span>{{ $post->likes_count ?? 0 }}</span>
                </div>
            </a>
            @if(!$loop->last)
                <div class="border-b border-gray-100"></div>
            @endif
        @empty
            <p class="text-xs text-gray-400 text-center py-4">لا توجد مقالات</p>
        @endforelse
    </div>

    <!-- Most Read Posts -->
    <div x-show="activeTab === 'read'" class="space-y-3">
        @forelse($trendingMostReadPosts as $post)
            <a href="{{ route('post.show', $post->slug) }}" class="block group">
                <h4 class="text-sm font-bold text-gray-900 group-hover:text-brand-accent transition-colors line-clamp-2 mb-1">
                    {{ $post->title }}
                </h4>
                <div class="flex items-center gap-1 text-xs text-gray-500">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <span>{{ $post->views ?? 0 }}</span>
                </div>
            </a>
            @if(!$loop->last)
                <div class="border-b border-gray-100"></div>
            @endif
        @empty
            <p class="text-xs text-gray-400 text-center py-4">لا توجد مقالات</p>
        @endforelse
    </div>

</div>
