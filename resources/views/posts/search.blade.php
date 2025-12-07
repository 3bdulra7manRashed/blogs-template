@extends('layouts.blog')

@section('title', $query ? 'بحث: ' . $query . ' - ' . config('app.name') : 'بحث - ' . config('app.name'))

@section('content')
<div class="container mx-auto px-4 py-12 max-w-5xl">
    <div class="mb-16 text-center">
        <h1 class="text-4xl font-serif font-bold text-brand-primary mb-4">البحث</h1>
        <form action="{{ route('search') }}" method="GET" class="mt-6 max-w-xl mx-auto">
            <div class="relative">
                <input type="text" name="q" value="{{ $query }}" placeholder="اكتب كلمة البحث واضغط انتر..." class="w-full px-6 py-4 border border-gray-200 rounded-full focus:outline-none focus:ring-2 focus:ring-brand-accent focus:border-transparent text-center text-lg bg-gray-50 placeholder-gray-400">
                <button type="submit" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-brand-accent">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </div>
        </form>
    </div>

    @if($query)
        @if($posts->count() > 0)
            <p class="text-center text-brand-muted mb-12">تم العثور على {{ $posts->total() }} نتيجة لـ "{{ $query }}"</p>
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

                            @include('partials.share-buttons', ['post' => $post])
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-16" dir="ltr">
                {{ $posts->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-brand-muted text-lg">لم يتم العثور على نتائج لـ "{{ $query }}".</p>
                <p class="text-brand-muted mt-2">جرب كلمات أخرى أو <a href="{{ route('home') }}" class="text-brand-accent hover:underline">تصفح جميع المقالات</a>.</p>
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <p class="text-brand-muted text-lg">اكتب كلمة البحث أعلاه للبحث في المقالات.</p>
        </div>
    @endif
</div>
@endsection
