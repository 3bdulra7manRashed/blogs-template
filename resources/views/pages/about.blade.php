@extends('layouts.blog')

@section('title', 'من نحن - ' . config('app.name'))

@section('content')
<div class="container mx-auto px-4 py-12" dir="rtl">
    
    <!-- Hero Section -->
    <div class="text-center mb-16">
        <h1 class="text-4xl md:text-5xl font-serif font-bold text-brand-primary mb-4">عن المدونة وكاتبها</h1>
        <p class="text-gray-500 text-lg max-w-2xl mx-auto">رحلة في عالم الكتابة، التدوين، والإبداع.</p>
    </div>

    @if($user)
    <!-- Centered Single Column Layout -->
    <div class="max-w-4xl mx-auto">
        
        <!-- Featured Image Section -->
        <div class="mb-12 rounded-2xl overflow-hidden shadow-lg h-64 md:h-96 bg-gray-50 border border-gray-100">
            <!-- TODO: Replace this source with your specific image URL -->
            <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" 
                 alt="About Me" 
                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
        </div>

        <!-- Biography Content (Without Card) -->
        <article class="prose prose-lg prose-slate max-w-none 
                        rtl:prose-p:text-right rtl:prose-headings:text-right 
                        font-serif prose-headings:text-brand-accent prose-headings:font-bold
                        prose-p:text-gray-600 prose-p:leading-loose">
            @if($user->biography)
                {!! $user->biography !!}
            @else
                <p class="text-gray-500 text-center py-8">لم تتم إضافة نبذة تعريفية بعد.</p>
            @endif
        </article>

    </div>
    @else
        <div class="text-center py-20 bg-white rounded-xl shadow-sm">
            <p class="text-gray-500 text-lg">لم يتم العثور على معلومات الكاتب.</p>
        </div>
    @endif

</div>
@endsection
