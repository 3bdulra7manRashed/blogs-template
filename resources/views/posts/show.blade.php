@extends('layouts.blog')

@section('title', $post->title . ' - ' . config('app.name'))

@section('description', $post->excerpt ?? $post->meta['description'] ?? Str::limit(strip_tags($post->content), 160))

@section('keywords', $post->meta['keywords'] ?? '')

@section('og_type', 'article')

@section('og_image', $post->featured_image_url ?? asset('images/default-share.jpg'))

@push('styles')
<style>
    /* Post Title Line Height for Better Multi-line Spacing */
    .post-title {
        line-height: 1.6 !important;
    }
    
    .post-title span {
        line-height: inherit !important;
        display: inline;
    }
</style>
@endpush

{{-- Additional meta tags can still be added via @push('meta') if needed --}}
@push('meta')
@if($post->published_at)
<meta property="article:published_time" content="{{ $post->published_at->toIso8601String() }}">
@endif
@if($post->author)
<meta property="article:author" content="{{ $post->author->name }}">
@endif
@if($post->categories->count() > 0)
@foreach($post->categories as $category)
<meta property="article:section" content="{{ $category->name }}">
@endforeach
@endif
@if($post->tags->count() > 0)
@foreach($post->tags as $tag)
<meta property="article:tag" content="{{ $tag->name }}">
@endforeach
@endif
@endpush

{{-- Structured Data (Schema.org JSON-LD) for Article --}}
@section('schema')
@php
    $schema = [
        "@context" => "https://schema.org",
        "@type" => "Article",
        "headline" => $post->title,
        // نستخدم دالة الهروب لضمان عدم وجود أكواد HTML تكسر الجيسون
        "description" => $post->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($post->content), 160),
        "datePublished" => $post->published_at?->toIso8601String() ?? $post->created_at->toIso8601String(),
        "dateModified" => $post->updated_at->toIso8601String(),
        "author" => [
            "@type" => "Person",
            "name" => $post->author->name
        ],
        "publisher" => [
            "@type" => "Organization",
            "name" => config('app.name', 'مدونة تجريبية'),
            "logo" => [
                "@type" => "ImageObject",
                "url" => asset('images/default-share.jpg')
            ]
        ],
        "mainEntityOfPage" => [
            "@type" => "WebPage",
            "@id" => url()->current()
        ]
    ];

    // إضافة الصورة شرطياً بطريقة برمجية نظيفة
    if ($post->featured_image_url) {
        $schema['image'] = $post->featured_image_url;
    }
@endphp

<script type="application/ld+json">
    {!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endsection

@section('content')
<!-- Reading Progress Bar -->
<div id="reading-progress-bar" class="fixed top-17 md:top-12 right-0 h-1.5 bg-brand-accent z-50 transition-all duration-100 ease-out" style="width: 0%;"></div>

<article>
    <!-- Post Header -->
    <div class="container mx-auto px-4 py-12 max-w-5xl">
        <header class="mb-10 text-center max-w-4xl mx-auto">
            @if($post->categories->count() > 0)
                <div class="flex items-center justify-center space-x-2 space-x-reverse mb-6">
                    @foreach($post->categories as $category)
                        <a href="{{ route('category.show', $category->slug) }}" class="text-sm font-bold text-brand-accent hover:text-brand-primary transition-colors">
                            {{ $category->name }}
                        </a>
                        @if(!$loop->last)
                            <span class="text-gray-300">/</span>
                        @endif
                    @endforeach
                </div>
            @endif

            <h1 class="post-title text-4xl md:text-5xl font-serif font-bold text-brand-primary mb-6">
                {{ $post->title }}
            </h1>

            <div class="flex items-center justify-center space-x-4 space-x-reverse text-sm text-gray-500 mb-8">
                <span>{{ $post->published_at->format('Y/m/d') }}</span>
            </div>
        </header>

        @if($post->featured_image_url)
            <div class="mb-12">
                <img src="{{ $post->featured_image_url }}" alt="{{ $post->featured_image_alt ?? $post->title }}" class="w-full aspect-video md:aspect-auto md:h-[500px] object-cover rounded-lg shadow-sm">
            </div>
        @endif

        <!-- Post Content -->
        <div class="prose prose-lg max-w-none prose-headings:text-brand-accent prose-headings:font-bold prose-p:text-gray-700 prose-p:leading-relaxed prose-a:text-blue-600 prose-img:rounded-xl prose-li:marker:text-brand-accent text-right">
            {!! $post->content !!}
        </div>

        <!-- Tags -->
        @if($post->tags->count() > 0)
            <div class="mt-12 pt-8 border-t border-gray-100 flex items-center space-x-2 space-x-reverse">
                <span class="text-sm font-bold text-brand-primary">الوسوم:</span>
                <div class="flex flex-wrap gap-2">
                    @foreach($post->tags as $tag)
                        <a href="{{ route('tag.show', $tag->slug) }}" class="text-sm text-gray-500 hover:text-brand-accent transition-colors">
                            #{{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Engagement Section -->
        <div class="max-w-3xl mx-auto mt-16 mb-12">
            
            <!-- Like Button -->
            <div class="flex justify-center mb-10">
                <button id="like-button" data-post-id="{{ $post->id }}" class="group relative flex items-center justify-center w-20 h-20 bg-white rounded-full shadow-[0_8px_30px_rgb(0,0,0,0.08)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.16)] transition-all duration-300 hover:scale-105 active:scale-95">
                    <!-- Filled Heart (Liked State) -->
                    <svg id="heart-filled" class="w-8 h-8 text-red-500 fill-current transition-all duration-300 group-hover:scale-110 hidden" viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                    <!-- Outline Heart (Unliked State) -->
                    <svg id="heart-outline" class="w-8 h-8 text-gray-400 transition-all duration-300 group-hover:scale-110 group-hover:text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <span id="likes-count" class="absolute -top-1 -right-1 w-8 h-8 bg-red-50 rounded-full flex items-center justify-center text-red-600 text-xs font-bold border-2 border-white shadow-sm transition-all duration-300">
                        {{ $post->likes_count > 0 ? ($post->likes_count > 99 ? '99+' : $post->likes_count) : '0' }}
                    </span>
                </button>
            </div>
            <!-- Share Buttons -->
            <div class="mb-10">
                @include('partials.share-buttons', ['post' => $post])
            </div>
            

            <!-- Author Bio Card -->
            <div class="bg-gray-50 rounded-lg p-6">
                <div class="flex flex-col sm:flex-row-reverse items-center sm:items-start gap-4">
                    
                    <!-- Avatar (Right Side) -->
                    <div class="flex-shrink-0">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($post->author->name) }}&background=random&size=128" alt="{{ $post->author->name }}" class="w-20 h-20 rounded-full object-cover shadow-md">
                    </div>

                    <!-- Text Content (Left Side) -->
                    <div class="flex-1 text-center sm:text-right">
                         <h3 class="text-xl font-serif font-bold text-brand-primary mb-2">{{ $post->author->name }}</h3>
                         <p class="text-gray-600 leading-relaxed">
                             {{ $post->author->short_bio ?? $post->author->bio ?? 'كاتب ومحرر متخصص في التقنية والتدوين الرقمي، يسعى لإثراء المحتوى العربي بمقالات عالية الجودة.' }}
                         </p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Post Navigation -->
    <div class="border-t border-gray-100 py-12 bg-white">
        <div class="container mx-auto px-4 max-w-5xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <!-- Previous Post (Right in RTL means Previous is actually Next logically in time, but usually Next button is on left in RTL) -->
                <!-- Let's keep Next on Left and Previous on Right for RTL flow -->
                
                <div class="relative group text-right">
                    @if($nextPost)
                        <div class="flex items-center space-x-4 space-x-reverse">
                            <div class="flex-shrink-0 hidden sm:block">
                                <a href="{{ route('post.show', $nextPost->slug) }}">
                                    @if($nextPost->featured_image_url)
                                        <img src="{{ $nextPost->featured_image_url }}" alt="{{ $nextPost->title }}" class="w-20 h-20 object-cover rounded-full opacity-80 group-hover:opacity-100 transition-opacity">
                                    @else
                                        <div class="w-20 h-20 rounded-full bg-gray-200"></div>
                                    @endif
                                </a>
                            </div>
                            <div>
                                <span class="block text-xs font-bold uppercase tracking-wide text-gray-400 mb-1">المقال التالي</span>
                                <a href="{{ route('post.show', $nextPost->slug) }}" class="block text-lg font-serif font-bold text-brand-primary group-hover:text-brand-accent transition-colors">
                                    {{ $nextPost->title }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="relative group text-left flex justify-end">
                    @if($previousPost)
                        <div class="flex items-center space-x-4 flex-row-reverse">
                            <div class="flex-shrink-0 hidden sm:block">
                                <a href="{{ route('post.show', $previousPost->slug) }}">
                                    @if($previousPost->featured_image_url)
                                        <img src="{{ $previousPost->featured_image_url }}" alt="{{ $previousPost->title }}" class="w-20 h-20 object-cover rounded-full opacity-80 group-hover:opacity-100 transition-opacity">
                                    @else
                                        <div class="w-20 h-20 rounded-full bg-gray-200"></div>
                                    @endif
                                </a>
                            </div>
                            <div>
                                <span class="block text-xs font-bold uppercase tracking-wide text-gray-400 mb-1">المقال السابق</span>
                                <a href="{{ route('post.show', $previousPost->slug) }}" class="block text-lg font-serif font-bold text-brand-primary group-hover:text-brand-accent transition-colors">
                                    {{ $previousPost->title }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Related Posts -->
    @if($relatedPosts->count() > 0)
        <div class="py-16 bg-gray-50 border-t border-gray-100">
            <div class="container mx-auto px-4 max-w-6xl">
                <h2 class="text-2xl font-serif font-bold mb-8 text-brand-primary text-right">مقالات ذات صلة</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($relatedPosts as $relatedPost)
                        <article class="group text-right">
                            @if($relatedPost->featured_image_url)
                                <a href="{{ route('post.show', $relatedPost->slug) }}" class="block mb-4 overflow-hidden rounded-lg">
                                    <img src="{{ $relatedPost->featured_image_url }}" alt="{{ $relatedPost->title }}" class="w-full aspect-video md:aspect-auto md:h-56 object-cover transform group-hover:scale-105 transition-transform duration-500">
                                </a>
                            @endif
                            <div class="mt-4">
                                <h3 class="text-xl font-serif font-bold mb-2 leading-tight">
                                    <a href="{{ route('post.show', $relatedPost->slug) }}" class="text-brand-primary hover:text-brand-accent transition-colors">
                                        {{ $relatedPost->title }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-500">{{ $relatedPost->published_at->format('Y/m/d') }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</article>

@push('scripts')
<script>
    // Reading Progress Bar
    (function() {
        const progressBar = document.getElementById('reading-progress-bar');
        
        function updateProgressBar() {
            // Calculate scroll percentage
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight;
            const clientHeight = document.documentElement.clientHeight;
            
            // Calculate percentage (0-100)
            const scrolled = (scrollTop / (scrollHeight - clientHeight)) * 100;
            
            // Update progress bar width
            progressBar.style.width = scrolled + '%';
        }
        
        // Update on scroll
        window.addEventListener('scroll', updateProgressBar, { passive: true });
        
        // Update on page load
        updateProgressBar();
        
        // Update on window resize (content height might change)
        window.addEventListener('resize', updateProgressBar, { passive: true });
    })();

    // Like Button Toggle Functionality
    (function() {
        const likeButton = document.getElementById('like-button');
        const likesCountElement = document.getElementById('likes-count');
        const heartFilled = document.getElementById('heart-filled');
        const heartOutline = document.getElementById('heart-outline');
        const postId = likeButton.dataset.postId;
        const storageKey = `hasLiked_${postId}`;
        
        let isLiked = localStorage.getItem(storageKey) === 'true';
        let isProcessing = false;
        
        // Set initial state based on localStorage
        function updateHeartVisual() {
            if (isLiked) {
                heartFilled.classList.remove('hidden');
                heartOutline.classList.add('hidden');
                likesCountElement.classList.add('bg-red-100');
                likesCountElement.classList.remove('bg-gray-100');
            } else {
                heartFilled.classList.add('hidden');
                heartOutline.classList.remove('hidden');
                likesCountElement.classList.remove('bg-red-100');
                likesCountElement.classList.add('bg-gray-100');
            }
        }
        
        // Initialize visual state
        updateHeartVisual();
        
        // Handle click
        likeButton.addEventListener('click', async function() {
            if (isProcessing) return;
            
            isProcessing = true;
            const action = isLiked ? 'unlike' : 'like';
            
            // Get current count (handle '99+' format)
            let currentCountText = likesCountElement.textContent;
            let currentCount = currentCountText.includes('+') ? 99 : parseInt(currentCountText) || 0;
            
            // Optimistic UI update
            if (action === 'like') {
                currentCount++;
                isLiked = true;
            } else {
                currentCount = Math.max(0, currentCount - 1);
                isLiked = false;
            }
            
            // Update visual immediately
            likesCountElement.textContent = currentCount > 99 ? '99+' : currentCount;
            updateHeartVisual();
            
            // Add animation feedback
            likeButton.classList.add('scale-110');
            setTimeout(() => likeButton.classList.remove('scale-110'), 200);
            
            try {
                // Send request to server
                const response = await fetch(`/api/posts/${postId}/like`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ action: action })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Update with actual count from server
                    likesCountElement.textContent = data.likes_count > 99 ? '99+' : data.likes_count;
                    
                    // Update localStorage
                    if (action === 'like') {
                        localStorage.setItem(storageKey, 'true');
                        isLiked = true;
                    } else {
                        localStorage.removeItem(storageKey);
                        isLiked = false;
                    }
                    
                    updateHeartVisual();
                } else {
                    // Revert on error
                    if (action === 'like') {
                        currentCount = Math.max(0, currentCount - 1);
                        isLiked = false;
                    } else {
                        currentCount++;
                        isLiked = true;
                    }
                    likesCountElement.textContent = currentCount > 99 ? '99+' : currentCount;
                    updateHeartVisual();
                }
            } catch (error) {
                console.error('Error toggling like:', error);
                // Revert on error
                if (action === 'like') {
                    currentCount = Math.max(0, currentCount - 1);
                    isLiked = false;
                } else {
                    currentCount++;
                    isLiked = true;
                }
                likesCountElement.textContent = currentCount > 99 ? '99+' : currentCount;
                updateHeartVisual();
            } finally {
                isProcessing = false;
            }
        });
    })();
</script>
@endpush
@endsection
