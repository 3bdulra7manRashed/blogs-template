@extends('layouts.blog')

@section('title', 'المقالات الأكثر إعجاباً - ' . config('app.name'))

@section('content')
<div class="container mx-auto px-4 py-12 max-w-5xl">
    
    <!-- Page Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-serif font-bold text-brand-primary mb-3">المقالات الأكثر إعجاباً</h1>
        <p class="text-gray-500">المقالات التي نالت إعجاب القراء</p>
    </div>

    @if($posts->count() > 0)
        <div class="space-y-20">
            @foreach($posts as $post)
                <article class="border-b border-gray-100 pb-16 last:border-0">
                    @if($post->featured_image_url)
                        <a href="{{ route('post.show', $post->slug) }}" class="block mb-10">
                            <img src="{{ $post->featured_image_url }}" alt="{{ $post->featured_image_alt ?? $post->title }}" class="w-full aspect-video md:aspect-auto md:h-[450px] object-cover rounded-lg shadow-sm hover:shadow-md transition-shadow">
                        </a>
                    @endif
                    
                    <div class="text-center max-w-4xl mx-auto">
                        <h2 class="text-3xl md:text-4xl font-serif font-bold mb-6 leading-tight text-brand-primary">
                            <a href="{{ route('post.show', $post->slug) }}" class="hover:text-brand-accent transition-colors">
                                {{ $post->title }}
                            </a>
                        </h2>

                        @if($post->excerpt)
                            <p class="text-gray-600 leading-loose mb-8 text-base md:text-lg">{{ $post->excerpt }}</p>
                        @endif

                        <div class="flex items-center justify-center gap-4 text-sm text-gray-500 mb-6">
                            <span>{{ $post->published_at->format('Y/m/d') }}</span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-red-500 fill-current" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                                {{ $post->likes_count ?? 0 }}
                            </span>
                        </div>

                        @include('partials.share-buttons', ['post' => $post])
                    </div>
                </article>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-16">
            {{ $posts->links() }}
        </div>
    @else
        <div class="text-center py-20">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-gray-500 text-lg">لا توجد مقالات</p>
        </div>
    @endif

</div>
@endsection

