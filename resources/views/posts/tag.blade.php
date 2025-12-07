@extends('layouts.blog')

@section('title', $tag->name . ' - ' . config('app.name'))

@section('content')
<div class="container mx-auto px-4 py-12 max-w-5xl">
    <div class="mb-16 text-center">
        <h1 class="text-4xl font-serif font-bold text-brand-primary mb-2">الوسم: {{ $tag->name }}</h1>
        <p class="text-brand-muted">مقالات موسومة بـ "{{ $tag->name }}"</p>
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

                        <div class="flex items-center justify-center gap-4">
                            <button class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center text-gray-500 hover:border-brand-accent hover:text-brand-accent transition-colors">
                                <svg class="w-4 h-4 transform -scale-x-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                            </button>
                            <a href="{{ route('post.show', $post->slug) }}" class="inline-block px-8 py-2 rounded-full border border-gray-300 text-brand-primary font-medium hover:border-brand-accent hover:text-brand-accent transition-colors">
                                أكمل القراءة
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-16" dir="ltr">
            {{ $posts->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <p class="text-brand-muted text-lg">لا توجد مقالات بهذا الوسم.</p>
            <a href="{{ route('home') }}" class="mt-4 inline-block text-brand-accent hover:underline">← العودة للرئيسية</a>
        </div>
    @endif
</div>
@endsection
