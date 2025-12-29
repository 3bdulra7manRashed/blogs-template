<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    {{-- Essential Meta Tags --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#c37c54">

    {{-- Primary Meta Tags --}}
    <title>@yield('title', config('app.name', 'مدونة تجريبية'))</title>
    <meta name="title" content="@yield('title', config('app.name', 'مدونة تجريبية'))">
    <meta name="description" content="@yield('description', 'مدونة عربية متخصصة في المقالات والمواضيع المتنوعة. اكتشف أحدث المقالات والمحتوى المميز.')">
    @hasSection('keywords')
    <meta name="keywords" content="@yield('keywords')">
    @endif
    <meta name="author" content="@yield('author', config('app.name', 'مدونة تجريبية'))">

    {{-- Canonical URL (Prevents Duplicate Content) --}}
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph / Facebook / LinkedIn --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', config('app.name', 'مدونة تجريبية'))">
    <meta property="og:description" content="@yield('description', 'مدونة عربية متخصصة في المقالات والمواضيع المتنوعة. اكتشف أحدث المقالات والمحتوى المميز.')">
    <meta property="og:image" content="@yield('og_image', asset('images/default-share.jpg'))">
    <meta property="og:site_name" content="{{ config('app.name', 'مدونة تجريبية') }}">
    <meta property="og:locale" content="ar_AR">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('title', config('app.name', 'مدونة تجريبية'))">
    <meta name="twitter:description" content="@yield('description', 'مدونة عربية متخصصة في المقالات والمواضيع المتنوعة. اكتشف أحدث المقالات والمحتوى المميز.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/default-share.jpg'))">

    {{-- Additional Meta Tags (Allow pages to override via @push('meta')) --}}
    @stack('meta')

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Scripts --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Structured Data (Schema.org JSON-LD) --}}
    @yield('schema')

    {{-- Google Analytics (GA4) - Uncomment and replace G-XXXXXXXXXX with your tracking ID --}}
    {{--
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-XXXXXXXXXX');
    </script>
    --}}
</head>
<body class="font-sans antialiased bg-white text-brand-primary flex flex-col min-h-screen">
    {{-- Skip to Content Link (Accessibility) --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:right-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-brand-accent focus:text-white focus:rounded-md focus:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-accent">
        التخطي إلى المحتوى الرئيسي
    </a>
    <!-- Top Navigation Bar (Hidden on mobile, visible on desktop) -->
    <div class="hidden md:block border-b border-gray-100 bg-white sticky top-0 z-40">
        <div class="container mx-auto px-4 max-w-5xl">
            <div class="flex items-center justify-between h-12">
                <!-- Right: Navigation Links (RTL: Right side) -->
                <nav class="hidden md:flex items-center space-x-6 space-x-reverse">
                    <!-- المقالات Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="flex items-center gap-1 text-m font-medium text-gray-800 hover:text-brand-accent transition-colors">
                            <span>المقالات</span>
                            <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg border border-gray-100 py-1 z-50"
                             style="display: none;">
                            <a href="{{ route('home') }}" class="block px-4 py-2 text-ms text-gray-700 hover:bg-gray-50 hover:text-brand-accent transition-colors">
                                المقالات الحديثة
                            </a>
                            <a href="{{ route('posts.most-liked') }}" class="block px-4 py-2 text-ms text-gray-700 hover:bg-gray-50 hover:text-brand-accent transition-colors">
                                المقالات الأكثر إعجاباً
                            </a>
                            <a href="{{ route('posts.most-read') }}" class="block px-4 py-2 text-ms text-gray-700 hover:bg-gray-50 hover:text-brand-accent transition-colors">
                                المقالات الأكثر قراءة
                            </a>
                        </div>
                    </div>
                    
                    <a href="{{ route('about') }}" class="text-m font-medium text-gray-800 hover:text-brand-accent transition-colors">
                        عني
                    </a>
                    <a href="{{ route('contact') }}" class="text-m font-medium text-gray-800 hover:text-brand-accent transition-colors">
                        تواصل معي
                    </a>
                </nav>

                <!-- Left: Social Icons & Search (RTL: Left side, but icons ordered LTR) -->
                <div class="flex flex-row-reverse items-center gap-5 ml-0 md:mr-auto">
                     <!-- Search Form (Expandable) -->
                     <div class="relative flex items-center" id="search-container">
                        <form id="search-form" action="{{ route('search') }}" method="GET" class="flex items-center flex-row-reverse transition-all duration-300">
                            <button type="button" id="search-toggle" class="text-gray-600 hover:text-brand-primary transition-colors z-10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>
                            <div id="search-input-wrapper" class="w-0 overflow-hidden transition-all duration-300 ease-in-out">
                                <input type="text" name="q" placeholder="اضغط للبحث" class="w-48 px-3 py-1 text-sm border-b border-gray-500 focus:border-brand-accent focus:outline-none bg-transparent text-gray-600 placeholder-gray-400 mr-2 text-right dir-rtl">
                            </div>
                        </form>
                    </div>

                    <!-- Sticky Menu Button (Hidden by default, appears on scroll) -->
                    <div id="sticky-menu-btn-container" class="w-0 h-8 overflow-hidden transition-all duration-300 ease-in-out opacity-0 pointer-events-none">
                        <button id="sticky-menu-btn" class="p-1 rounded-full border border-gray-200 hover:border-brand-accent hover:text-brand-accent bg-gray-50 text-gray-600 flex items-center justify-center w-8 h-8 flex-shrink-0">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                    </div>
                    
                    <a href="#" class="text-gray-500 hover:text-brand-primary transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-brand-primary transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="#" class="text-gray-500 hover:text-brand-primary transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Header (Logo & Menu Button) - Sticky on mobile -->
    <header class="bg-white sticky top-0 z-50 md:z-30 md:relative py-4 md:py-8 border-b border-gray-100 md:border-b-0" x-data="{ mobileMenuOpen: false, articlesOpen: false }" x-init="$watch('mobileMenuOpen', value => { if (value) { setTimeout(() => document.getElementById('mobile-menu-close')?.focus(), 100); } })">
        <div class="container mx-auto px-4 max-w-5xl">
            <div class="flex justify-between items-center">
                <!-- Right: Logo -->
                <a href="{{ route('home') }}" class="text-4xl md:text-4xl text-2xl font-serif font-bold text-brand-accent tracking-tight">
                    مدونة تجريبية
                </a>
                
                <!-- Left: Menu Button (Desktop) -->
                <div class="hidden md:block">
                     <button id="sidebar-toggle" class="p-3 border border-gray-200 rounded-full hover:border-brand-accent transition-colors bg-gray-50">
                         <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                     </button>
                </div>

                <!-- Mobile: Hamburger Button -->
                <button 
                    class="md:hidden text-gray-600 hover:text-brand-accent transition-colors p-2"
                    id="mobile-menu-button"
                    @click="mobileMenuOpen = true; $nextTick(() => { document.getElementById('mobile-menu-close').focus(); })"
                    aria-controls="mobile-menu"
                    :aria-expanded="mobileMenuOpen"
                    aria-label="فتح القائمة"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Off-Canvas Menu -->
        <!-- Overlay -->
        <div 
            x-show="mobileMenuOpen"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="mobileMenuOpen = false"
            @keydown.escape.window="mobileMenuOpen = false"
            class="fixed inset-0 bg-black bg-opacity-50 z-50 md:hidden"
            style="display: none;"
            aria-hidden="true"
        ></div>

        <!-- Sidebar Panel -->
        <div 
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-300 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            @keydown.escape.window="mobileMenuOpen = false"
            id="mobile-menu"
            role="dialog"
            aria-modal="true"
            aria-label="قائمة الموقع"
            class="fixed top-0 right-0 h-full w-72 sm:w-80 bg-white shadow-xl z-50 overflow-y-auto md:hidden"
            style="display: none;"
            x-ref="mobileMenu"
        >
            <!-- Close Button -->
            <div class="flex justify-start items-center p-4 border-b border-gray-100">
                <button 
                    id="mobile-menu-close"
                    @click="mobileMenuOpen = false"
                    class="p-2 text-gray-600 hover:text-brand-accent transition-colors rounded-md hover:bg-gray-100"
                    aria-label="إغلاق القائمة"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Navigation Items -->
            <nav class="py-4" @click.away="mobileMenuOpen = false">
                <div class="flex flex-col">
                    <!-- المقالات (Articles) - Accordion -->
                    <div class="border-b border-gray-100">
                        <button 
                            @click="articlesOpen = !articlesOpen"
                            class="w-full flex items-center justify-between px-4 py-3 text-right text-base font-medium text-gray-800 hover:bg-gray-50 hover:text-brand-accent transition-colors min-h-[3rem]"
                            :aria-expanded="articlesOpen"
                            aria-controls="articles-submenu"
                        >
                            <span>المقالات</span>
                            <svg 
                                class="w-5 h-5 transition-transform duration-200"
                                :class="articlesOpen ? 'rotate-180' : ''"
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Articles Submenu -->
                        <div 
                            id="articles-submenu"
                            x-show="articlesOpen"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 max-h-0"
                            x-transition:enter-end="opacity-100 max-h-96"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 max-h-96"
                            x-transition:leave-end="opacity-0 max-h-0"
                            class="bg-gray-50 overflow-hidden"
                            style="display: none;"
                        >
                            <a 
                                href="{{ route('home') }}" 
                                class="block px-4 py-3 pr-8 text-right text-sm text-gray-700 hover:bg-gray-100 hover:text-brand-accent transition-colors min-h-[3rem] flex items-center {{ request()->routeIs('home') ? 'text-brand-accent font-medium bg-gray-100' : '' }}"
                                @click="mobileMenuOpen = false"
                            >
                                المقالات الحديثة
                            </a>
                            <a 
                                href="{{ route('posts.most-liked') }}" 
                                class="block px-4 py-3 pr-8 text-right text-sm text-gray-700 hover:bg-gray-100 hover:text-brand-accent transition-colors min-h-[3rem] flex items-center {{ request()->routeIs('posts.most-liked') ? 'text-brand-accent font-medium bg-gray-100' : '' }}"
                                @click="mobileMenuOpen = false"
                            >
                                المقالات الأكثر إعجاباً
                            </a>
                            <a 
                                href="{{ route('posts.most-read') }}" 
                                class="block px-4 py-3 pr-8 text-right text-sm text-gray-700 hover:bg-gray-100 hover:text-brand-accent transition-colors min-h-[3rem] flex items-center {{ request()->routeIs('posts.most-read') ? 'text-brand-accent font-medium bg-gray-100' : '' }}"
                                @click="mobileMenuOpen = false"
                            >
                                المقالات الأكثر قراءة
                            </a>
                        </div>
                    </div>

                    <!-- عني (About) -->
                    <a 
                        href="{{ route('about') }}" 
                        class="block px-4 py-3 text-right text-base font-medium text-gray-800 hover:bg-gray-50 hover:text-brand-accent transition-colors min-h-[3rem] flex items-center border-b border-gray-100 {{ request()->routeIs('about') ? 'text-brand-accent bg-gray-50' : '' }}"
                        @click="mobileMenuOpen = false"
                    >
                        عني
                    </a>

                    <!-- تواصل معي (Contact) -->
                    <a 
                        href="{{ route('contact') }}" 
                        class="block px-4 py-3 text-right text-base font-medium text-gray-800 hover:bg-gray-50 hover:text-brand-accent transition-colors min-h-[3rem] flex items-center border-b border-gray-100 {{ request()->routeIs('contact') ? 'text-brand-accent bg-gray-50' : '' }}"
                        @click="mobileMenuOpen = false"
                    >
                        تواصل معي
                    </a>

                    <!-- Sidebar Widgets (Mobile Only) -->
                    <div class="px-4 py-4 space-y-4 border-b border-gray-100">
                        <!-- Search -->
                        <div class="bg-brand-secondary p-6 rounded-lg">
                            <h4 class="text-base font-semibold mb-3 text-right">البحث</h4>
                            <form action="{{ route('search') }}" method="GET" @click.stop>
                                <input 
                                    type="search" 
                                    name="q" 
                                    value="{{ request('q') }}"
                                    placeholder="ابحث عن مقالات..." 
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-right focus:outline-none focus:ring-2 focus:ring-brand-accent focus:border-transparent"
                                />
                            </form>
                        </div>

                        <!-- Categories -->
                        @if(isset($categories) && $categories->count() > 0)
                            <div class="bg-brand-secondary p-6 rounded-lg">
                                <h4 class="text-base font-semibold mb-3 text-right">الأقسام</h4>
                                <ul class="space-y-2 text-right">
                                    @foreach($categories as $category)
                                        <li>
                                            <a 
                                                href="{{ route('category.show', $category->slug) }}" 
                                                class="flex justify-between items-center py-2 hover:text-brand-accent transition-colors"
                                                @click="mobileMenuOpen = false"
                                            >
                                                <span class="text-sm text-gray-500">({{ $category->posts_count ?? 0 }})</span>
                                                <span class="text-sm font-medium">{{ $category->name }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Latest Posts -->
                        @if(isset($recentPosts) && $recentPosts->count() > 0)
                            <div class="bg-brand-secondary p-6 rounded-lg">
                                <h4 class="text-base font-semibold mb-3 text-right">أحدث المقالات</h4>
                                <ul class="space-y-3 text-right">
                                    @foreach($recentPosts as $recentPost)
                                        <li>
                                            <a 
                                                href="{{ route('post.show', $recentPost->slug) }}" 
                                                class="block group"
                                                @click="mobileMenuOpen = false"
                                            >
                                                <div class="text-sm font-medium group-hover:text-brand-accent transition-colors mb-1">
                                                    {{ Str::limit($recentPost->title, 60) }}
                                                </div>
                                                <div class="text-xs text-gray-400">
                                                    {{ $recentPost->published_at->format('Y/m/d') }}
                                                </div>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                </div>
            </nav>
        </div>
    </header>

    <!-- No-JS Fallback Menu (shows when JavaScript is disabled) -->
    <noscript>
        <div class="md:hidden border-t border-gray-200 bg-white">
            <div class="container mx-auto px-4 py-4 flex flex-col space-y-4">
                <details class="border-b border-gray-100 pb-4">
                    <summary class="text-base font-medium text-gray-800 cursor-pointer list-none py-2">
                        المقالات
                    </summary>
                    <div class="mt-2 pr-4 space-y-2">
                        <a href="{{ route('home') }}" class="block text-sm text-gray-700 hover:text-brand-accent py-2">المقالات الحديثة</a>
                        <a href="{{ route('posts.most-liked') }}" class="block text-sm text-gray-700 hover:text-brand-accent py-2">المقالات الأكثر إعجاباً</a>
                        <a href="{{ route('posts.most-read') }}" class="block text-sm text-gray-700 hover:text-brand-accent py-2">المقالات الأكثر قراءة</a>
                    </div>
                </details>
                <a href="{{ route('about') }}" class="text-base font-medium text-gray-800 hover:text-brand-accent py-2">عني</a>
                <a href="{{ route('contact') }}" class="text-base font-medium text-gray-800 hover:text-brand-accent py-2">تواصل معي</a>
                
                <!-- Sidebar Widgets (No-JS Fallback) -->
                <div class="space-y-4 pt-4 border-t border-gray-100">
                    <!-- Search -->
                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                        <h4 class="text-base font-semibold mb-3 text-right">البحث</h4>
                        <form action="{{ route('search') }}" method="GET">
                            <input 
                                type="search" 
                                name="q" 
                                value="{{ request('q') }}"
                                placeholder="ابحث عن مقالات..." 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-right focus:outline-none focus:ring-2 focus:ring-brand-accent focus:border-transparent"
                            />
                        </form>
                    </div>

                    <!-- Categories -->
                    @if(isset($categories) && $categories->count() > 0)
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                            <h4 class="text-base font-semibold mb-3 text-right">الأقسام</h4>
                            <ul class="space-y-2 text-right">
                                @foreach($categories as $category)
                                    <li>
                                        <a href="{{ route('category.show', $category->slug) }}" class="flex justify-between items-center py-2 hover:text-brand-accent transition-colors">
                                            <span class="text-sm text-gray-500">({{ $category->posts_count ?? 0 }})</span>
                                            <span class="text-sm font-medium">{{ $category->name }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Latest Posts -->
                    @if(isset($recentPosts) && $recentPosts->count() > 0)
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm">
                            <h4 class="text-base font-semibold mb-3 text-right">أحدث المقالات</h4>
                            <ul class="space-y-3 text-right">
                                @foreach($recentPosts as $recentPost)
                                    <li>
                                        <a href="{{ route('post.show', $recentPost->slug) }}" class="block group">
                                            <div class="text-sm font-medium group-hover:text-brand-accent transition-colors mb-1">
                                                {{ Str::limit($recentPost->title, 60) }}
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                {{ $recentPost->published_at->format('Y/m/d') }}
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </noscript>

    <!-- Main Content -->
    <main id="main-content" class="flex-grow">
        {{-- Flash Messages Area --}}
        @if(session('success'))
            <div class="container mx-auto px-4 mt-6 max-w-5xl">
                <div class="bg-green-50 border-r-4 border-green-500 text-green-700 p-4 rounded shadow-sm relative" role="alert" x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <p class="font-medium">{{ session('success') }}</p>
                        </div>
                        <button @click="show = false" class="text-green-700 hover:text-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 rounded">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container mx-auto px-4 mt-6 max-w-5xl">
                <div class="bg-red-50 border-r-4 border-red-500 text-red-700 p-4 rounded shadow-sm relative" role="alert" x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <p class="font-medium">{{ session('error') }}</p>
                        </div>
                        <button @click="show = false" class="text-red-700 hover:text-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 rounded">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    @include('partials.sidebar')

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 mt-20">
        <div class="container mx-auto px-4 py-12 max-w-5xl">
             <div class="flex flex-col md:flex-row justify-between items-center">
                 <div class="mb-4 md:mb-0">
                     <p class="text-sm text-gray-500">&copy; {{ date('Y') }} {{ config('app.name', 'Writer Blog') }}. جميع الحقوق محفوظة.</p>
                 </div>
                 <div class="flex space-x-6 space-x-reverse">
                     <a href="#" class="text-gray-400 hover:text-brand-primary transition-colors"><span class="sr-only">Facebook</span><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
                     <a href="#" class="text-gray-400 hover:text-brand-primary transition-colors"><span class="sr-only">Twitter</span><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>
                     <a href="#" class="text-gray-400 hover:text-brand-primary transition-colors"><span class="sr-only">Instagram</span><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
                 </div>
             </div>
        </div>
    </footer>

    <script>
        // Focus trap for mobile menu - simplified approach
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenuClose = document.getElementById('mobile-menu-close');
            
            if (mobileMenu) {
                // Handle Tab key for focus trap
                mobileMenu.addEventListener('keydown', function(e) {
                    if (e.key !== 'Tab') return;
                    
                    const focusableElements = mobileMenu.querySelectorAll(
                        'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])'
                    );
                    const firstElement = focusableElements[0];
                    const lastElement = focusableElements[focusableElements.length - 1];
                    
                    if (e.shiftKey) {
                        // Shift + Tab
                        if (document.activeElement === firstElement) {
                            e.preventDefault();
                            lastElement?.focus();
                        }
                    } else {
                        // Tab
                        if (document.activeElement === lastElement) {
                            e.preventDefault();
                            firstElement?.focus();
                        }
                    }
                });
            }
        });

        // Sidebar toggle
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        const closeSidebar = document.getElementById('close-sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        function toggleSidebar() {
            const isHidden = sidebar.classList.contains('hidden');
            
            // In RTL, the sidebar should come from the left if the button is on the left?
            // The third screenshot shows sidebar on the LEFT.
            // So we need to change translate-x-full to -translate-x-full for left side sidebar
            // Or adjust the sidebar partial to be on the left.
            
            if (isHidden) {
                sidebar.classList.remove('hidden');
                overlay.classList.remove('hidden');
                setTimeout(() => {
                    sidebar.classList.remove('-translate-x-full'); // Changed for Left Sidebar
                    overlay.classList.remove('opacity-0');
                }, 10);
            } else {
                sidebar.classList.add('-translate-x-full'); // Changed for Left Sidebar
                overlay.classList.add('opacity-0');
                setTimeout(() => {
                    sidebar.classList.add('hidden');
                    overlay.classList.add('hidden');
                }, 300);
            }
        }

        sidebarToggle?.addEventListener('click', toggleSidebar);
        closeSidebar?.addEventListener('click', toggleSidebar);
        overlay?.addEventListener('click', toggleSidebar);
        
        // --- 1. SEARCH LOGIC ---
        const searchToggle = document.getElementById('search-toggle');
        const searchInputWrapper = document.getElementById('search-input-wrapper');
        const searchContainer = document.getElementById('search-container');
        const searchForm = document.getElementById('search-form'); // Select the form

        searchToggle?.addEventListener('click', (e) => {
            e.preventDefault();
            if (searchInputWrapper.classList.contains('w-0')) {
                // OPEN
                searchInputWrapper.classList.remove('w-0');
                searchInputWrapper.classList.add('w-48');
                searchForm.classList.add('gap-3'); // Add Gap
                setTimeout(() => searchInputWrapper.querySelector('input').focus(), 300);
            } else {
                // CLOSE
                searchInputWrapper.classList.remove('w-48');
                searchInputWrapper.classList.add('w-0');
                searchForm.classList.remove('gap-3'); // Remove Gap
            }
        });

        document.addEventListener('click', (e) => {
            if (searchContainer && !searchContainer.contains(e.target) && !searchInputWrapper.classList.contains('w-0')) {
                searchInputWrapper.classList.remove('w-48');
                searchInputWrapper.classList.add('w-0');
                searchForm.classList.remove('gap-3'); // Remove Gap
            }
        });

        // --- 2. STICKY MENU LOGIC ---
        const stickyMenuBtn = document.getElementById('sticky-menu-btn');
        const stickyMenuBtnContainer = document.getElementById('sticky-menu-btn-container');
        let isStickyVisible = false;

        // Function to handle Sidebar toggle (reused)
        stickyMenuBtn?.addEventListener('click', toggleSidebar); 

        window.addEventListener('scroll', function() {
            if (window.scrollY > 150) { 
                // SHOW BUTTON - Expand from 0 to full width
                if (!isStickyVisible) {
                    isStickyVisible = true;
                    
                    // Expand width and fade in
                    stickyMenuBtnContainer.classList.remove('w-0', 'opacity-0', 'pointer-events-none');
                    stickyMenuBtnContainer.classList.add('w-8', 'opacity-100', 'pointer-events-auto');
                }
            } else {
                // HIDE BUTTON - Collapse to 0 width
                if (isStickyVisible) {
                    isStickyVisible = false;
                    
                    // Collapse width and fade out
                    stickyMenuBtnContainer.classList.remove('w-8', 'opacity-100', 'pointer-events-auto');
                    stickyMenuBtnContainer.classList.add('w-0', 'opacity-0', 'pointer-events-none');
                }
            }
        });

        // Web Share API
        function sharePost(title, url) {
            if (navigator.share) {
                navigator.share({
                    title: title,
                    url: url
                }).catch(console.error);
            } else {
                // Fallback - Copy to clipboard or simple alert
                navigator.clipboard.writeText(url).then(() => {
                    alert('تم نسخ الرابط للحافظة!');
                });
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
