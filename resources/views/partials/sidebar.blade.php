<aside id="sidebar" class="hidden fixed inset-y-0 left-0 w-80 bg-white shadow-xl z-50 overflow-y-auto transform transition-transform duration-300 -translate-x-full border-r border-gray-100">
    <div class="p-8">
        <div class="flex justify-between items-center mb-8">
             <!-- Close button on the right for RTL sidebar on left -->
            <button id="close-sidebar" class="text-gray-400 hover:text-brand-primary">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>    
        </div>

        <div class="space-y-8">
            <!-- Search -->
            <div class="bg-brand-secondary p-6 rounded-lg">
                <h3 class="text-lg font-serif font-semibold mb-4">البحث</h3>
                <form action="{{ route('search') }}" method="GET">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="ابحث عن مقالات..." class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-accent focus:border-transparent">
                </form>
            </div>

            <!-- Categories -->
            @if(isset($categories) && $categories->count() > 0)
                <div class="bg-brand-secondary p-6 rounded-lg">
                    <h3 class="text-lg font-serif font-semibold mb-4">الأقسام</h3>
                    <ul class="space-y-2">
                        @foreach($categories as $category)
                            <li>
                                <a href="{{ route('category.show', $category->slug) }}" class="flex items-center justify-between text-sm hover:text-brand-accent transition-colors">
                                    <span>{{ $category->name }}</span>
                                    <span class="text-brand-muted">({{ $category->posts_count }})</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Recent Posts -->
            @if(isset($recentPosts) && $recentPosts->count() > 0)
                <div class="bg-brand-secondary p-6 rounded-lg">
                    <h3 class="text-lg font-serif font-semibold mb-4">أحدث المقالات</h3>
                    <ul class="space-y-4">
                        @foreach($recentPosts as $recentPost)
                            <li>
                                <a href="{{ route('post.show', $recentPost->slug) }}" class="block group">
                                    <h4 class="text-sm font-medium group-hover:text-brand-accent transition-colors mb-1">
                                        {{ $recentPost->title }}
                                    </h4>
                                    <p class="text-xs text-brand-muted">{{ $recentPost->published_at->format('Y/m/d') }}</p>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</aside>
<div id="sidebar-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity duration-300 opacity-0"></div>
