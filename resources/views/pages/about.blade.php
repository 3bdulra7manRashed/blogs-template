@extends('layouts.blog')

@section('title', 'عني - ' . config('app.name'))

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
        
        <!-- Featured Image Section - Author Profile Photo -->
        <div class="mb-12 rounded-2xl overflow-hidden shadow-lg h-64 md:h-96 bg-gray-50 border border-gray-100">
            <img src="{{ $user->profile_photo_url }}" 
                 alt="{{ $user->name }}" 
                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
        </div>

        <!-- Biography Content -->
        <div class="prose prose-lg max-w-none prose-headings:text-brand-accent prose-li:marker:text-brand-accent text-right" dir="rtl">
            @if($user->biography)
                {!! $user->biography !!}
            @else
                <p class="text-gray-500 text-center py-8">لم تتم إضافة نبذة تعريفية بعد.</p>
            @endif
        </div>

    </div>
    @else
        <div class="text-center py-20 bg-white rounded-xl shadow-sm">
            <p class="text-gray-500 text-lg">لم يتم العثور على معلومات الكاتب.</p>
        </div>
    @endif

</div>
@endsection

@push('styles')
<style>
    /* CKEditor Content Styles for Biography Display */
    .ckeditor-content {
        font-family: 'Cairo', sans-serif;
        line-height: 1.8;
        direction: rtl;
    }
    
    /* Paragraphs */
    .ckeditor-content p {
        margin-bottom: 1.2em;
        color: #4b5563;
        font-size: 1.125rem;
        line-height: 1.9;
    }
    
    /* Headings */
    .ckeditor-content h1,
    .ckeditor-content h2,
    .ckeditor-content h3,
    .ckeditor-content h4,
    .ckeditor-content h5,
    .ckeditor-content h6 {
        color: #c37c54;
        font-weight: 700;
        margin-top: 1.5em;
        margin-bottom: 0.8em;
        line-height: 1.4;
    }
    
    .ckeditor-content h1 { font-size: 2em; }
    .ckeditor-content h2 { font-size: 1.75em; }
    .ckeditor-content h3 { font-size: 1.5em; }
    .ckeditor-content h4 { font-size: 1.25em; }
    
    /* Lists */
    .ckeditor-content ul,
    .ckeditor-content ol {
        margin: 1.5em 0;
        padding-right: 2rem;
        padding-left: 0;
    }
    
    .ckeditor-content ul {
        list-style-type: disc;
    }
    
    .ckeditor-content ol {
        list-style-type: decimal;
    }
    
    .ckeditor-content li {
        margin-bottom: 0.8em;
        color: #4b5563;
        font-size: 1.125rem;
        line-height: 1.8;
    }
    
    .ckeditor-content ul ul,
    .ckeditor-content ol ul {
        list-style-type: circle;
        margin-top: 0.5em;
    }
    
    .ckeditor-content ul ul ul,
    .ckeditor-content ol ul ul {
        list-style-type: square;
    }
    
    /* Blockquotes */
    .ckeditor-content blockquote {
        border-right: 4px solid #c37c54;
        border-left: none;
        margin: 2em 0;
        padding: 1.5em 2em;
        background: #f5f0ea;
        border-radius: 0.5rem;
        font-style: italic;
        color: #1f1f1f;
        position: relative;
    }
    
    .ckeditor-content blockquote::before {
        content: '"';
        font-size: 4rem;
        color: #c37c54;
        opacity: 0.3;
        position: absolute;
        top: -0.5rem;
        right: 0.5rem;
        font-family: Georgia, serif;
    }
    
    .ckeditor-content blockquote p {
        margin: 0;
        font-size: 1.125rem;
        line-height: 1.8;
    }
    
    /* Links */
    .ckeditor-content a {
        color: #c37c54;
        text-decoration: underline;
        transition: color 0.2s;
    }
    
    .ckeditor-content a:hover {
        color: #a86845;
    }
    
    /* Strong and Emphasis */
    .ckeditor-content strong,
    .ckeditor-content b {
        font-weight: 700;
        color: #1f1f1f;
    }
    
    .ckeditor-content em,
    .ckeditor-content i {
        font-style: italic;
    }
    
    /* Tables */
    .ckeditor-content table {
        width: 100%;
        margin: 2em 0;
        border-collapse: collapse;
        direction: rtl;
    }
    
    .ckeditor-content table th,
    .ckeditor-content table td {
        border: 1px solid #e5e7eb;
        padding: 0.75rem 1rem;
        text-align: right;
    }
    
    .ckeditor-content table th {
        background: #f9fafb;
        font-weight: 600;
        color: #1f1f1f;
    }
    
    .ckeditor-content table td {
        background: white;
    }
    
    .ckeditor-content table tr:hover td {
        background: #f9fafb;
    }
    
    /* Images */
    .ckeditor-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        margin: 2em 0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .ckeditor-content figure {
        margin: 2em 0;
    }
    
    .ckeditor-content figure img {
        margin: 0;
    }
    
    .ckeditor-content figcaption {
        text-align: center;
        font-size: 0.9rem;
        color: #6b7280;
        margin-top: 0.5rem;
        font-style: italic;
    }
    
    /* Code */
    .ckeditor-content code {
        background: #f3f4f6;
        padding: 0.2em 0.4em;
        border-radius: 0.25rem;
        font-family: 'Courier New', monospace;
        font-size: 0.9em;
        color: #1f1f1f;
    }
    
    .ckeditor-content pre {
        background: #1f2937;
        color: #f9fafb;
        padding: 1.5em;
        border-radius: 0.5rem;
        overflow-x: auto;
        margin: 2em 0;
    }
    
    .ckeditor-content pre code {
        background: none;
        padding: 0;
        color: inherit;
    }
    
    /* Horizontal Rule */
    .ckeditor-content hr {
        border: none;
        border-top: 2px solid #e5e7eb;
        margin: 3em 0;
    }
</style>
@endpush
