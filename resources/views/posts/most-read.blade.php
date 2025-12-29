@extends('layouts.blog')

@section('title', 'المقالات الأكثر قراءة - ' . config('app.name'))

@section('content')
<div class="container mx-auto px-4 py-12 max-w-5xl">
    
    <!-- Page Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-serif font-bold text-brand-primary mb-3">المقالات الأكثر قراءة</h1>
        <p class="text-gray-500">المقالات الأكثر مشاهدة من قبل القراء</p>
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
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                {{ $post->views ?? 0 }}
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

