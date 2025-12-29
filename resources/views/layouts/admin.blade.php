<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'لوحة التحكم') - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans">
    {{-- Plain password display (if needed) --}}
    @php
        $plainPassword = session()->pull('plain_password'); // consumed once
    @endphp
    @if($plainPassword)
        <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded-lg shadow-sm" role="alert" x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-center">
                <svg class="w-5 h-5 ml-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <p class="font-medium">كلمة المرور هي: <strong>{{ $plainPassword }}</strong> (تظهر مرة واحدة فقط)</p>
            </div>
        </div>
    @endif

    {{-- Global confirmation modal (centralized) --}}
    <div id="site-confirm-modal" aria-hidden="true" class="hidden fixed inset-0 z-[60] flex items-center justify-center">
        <div id="site-confirm-backdrop" class="absolute inset-0 bg-black/60"></div>

        <div role="dialog" aria-modal="true" aria-labelledby="site-confirm-title"
             class="relative bg-white rounded-lg shadow-2xl max-w-lg w-[90%] md:w-1/2 p-6 text-center mx-auto">
            <button id="site-confirm-close" aria-label="close"
                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 transition-colors text-2xl leading-none">&times;</button>

            <h3 id="site-confirm-title" class="text-xl font-semibold text-gray-800 mb-3">تأكيد الإجراء</h3>

            <p id="site-confirm-message" class="text-gray-600 mb-4">هل أنت متأكد؟</p>

            <div id="site-confirm-input-wrapper" class="mb-4 hidden">
                <input id="site-confirm-input" type="text"
                       class="w-full border rounded px-3 py-2 text-center"
                       placeholder="اكتب كلمة التأكيد هنا" />
                <p id="site-confirm-help" class="text-sm text-gray-500 mt-2">اكتب <strong>حذف</strong> للتأكيد</p>
                <p id="site-confirm-error" class="text-sm text-red-600 mt-2 hidden"></p>
            </div>

            <div class="flex items-center justify-center gap-4" dir="rtl">
                <button id="site-confirm-ok" class="px-6 py-2.5 rounded-lg bg-red-600 text-white font-medium hover:bg-red-700 transition-colors duration-200 shadow-sm hover:shadow-md">حذف</button>
                <button id="site-confirm-cancel" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 hover:border-gray-400 transition-colors duration-200">إلغاء</button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Button (visible only on mobile) -->
    <div class="md:hidden fixed top-0 left-0 right-0 bg-white shadow-md z-40 px-4 py-3 flex items-center justify-between">
        <a href="{{ route('admin.dashboard') }}" class="text-lg font-bold text-brand-accent hover:text-opacity-80 transition-colors">
            {{ config('app.name') }}
        </a>
        <button id="mobile-menu-btn" class="p-2 rounded-md text-gray-600 hover:text-brand-primary hover:bg-gray-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>

    <!-- Backdrop (mobile only) -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>

    <div class="min-h-screen flex pt-20 md:pt-0">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed top-0 right-0 h-full w-64 bg-white shadow-lg z-50 transform translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out overflow-y-auto">
            <div class="p-6 pt-6 md:pt-6">
                <h2 class="text-xl font-serif font-bold text-brand-primary mb-6">
                    <a href="{{ route('admin.dashboard') }}">{{ config('app.name') }}</a>
                </h2>
                <nav class="space-y-2">
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center px-4 py-2 rounded-md transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-brand-accent  text-white hover:text-white hover:bg-amber-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        الرئيسية
                    </a>
                
                    <a href="{{ route('admin.posts.index') }}"
                       class="flex items-center px-4 py-2 rounded-md transition-colors {{ request()->routeIs('admin.posts.*') ? 'bg-brand-accent  text-white hover:text-white hover:bg-amber-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        المقالات
                    </a>
                
                    <a href="{{ route('admin.categories.index') }}"
                       class="flex items-center px-4 py-2 rounded-md transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-brand-accent  text-white hover:text-white hover:bg-amber-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        الأقسام
                    </a>
                
                    <a href="{{ route('admin.tags.index') }}"
                       class="flex items-center px-4 py-2 rounded-md transition-colors {{ request()->routeIs('admin.tags.*') ? 'bg-brand-accent text-white hover:text-white hover:bg-amber-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                        </svg>
                        الوسوم
                    </a>
                
                    <a href="{{ route('admin.media.index') }}"
                       class="flex items-center px-4 py-2 rounded-md transition-colors {{ request()->routeIs('admin.media.*') ? 'bg-brand-accent text-white hover:text-white hover:bg-amber-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        الوسائط
                    </a>
                
                    @can('manage-users')
                    <div class="pt-4 border-t border-gray-200 mt-4">
                        <p class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">إدارة خاصة</p>
                        <a href="{{ route('admin.users.index') }}"
                           class="flex items-center px-4 py-2 rounded-md transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-brand-accent  text-white hover:text-white hover:bg-amber-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            المستخدمين
                        </a>
                    </div>
                    @endcan
                
                    <div class="pt-4 border-t border-gray-200 mt-4">
                        <a href="{{ route('home') }}" class="flex items-center px-4 py-2 rounded-md text-gray-700 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            عرض الموقع
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="mt-2">
                            @csrf
                            <button type="submit" class="flex items-center w-full text-right px-4 py-2 rounded-md text-gray-700 hover:bg-gray-100 transition-colors">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                تسجيل الخروج
                            </button>
                        </form>
                    </div>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 w-full md:mr-64 overflow-auto">
            <div class="p-4 md:p-8">
                {{-- Flash Alert (Success/Error) - Auto-dismissing --}}
                @if(session('success'))
                    <div id="flash-alert" class="mb-4 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm flex items-center justify-between" role="alert" x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 ml-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <p class="font-medium">{{ session('success') }}</p>
                        </div>
                        <button @click="show = false" class="text-green-700 hover:text-green-900 ml-4">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div id="flash-alert-error" class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm flex items-center justify-between" role="alert" x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 ml-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <p class="font-medium">{{ session('error') }}</p>
                        </div>
                        <button @click="show = false" class="text-red-700 hover:text-red-900 ml-4">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
    
    @stack('scripts')
    
    <script>
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        
        function toggleSidebar() {
            if (sidebar && backdrop) {
                sidebar.classList.toggle('translate-x-full');
                backdrop.classList.toggle('hidden');
                document.body.classList.toggle('overflow-hidden');
            }
        }
        
        mobileMenuBtn?.addEventListener('click', toggleSidebar);
        backdrop?.addEventListener('click', toggleSidebar);
        
        // Close sidebar when clicking a link on mobile
        if (window.innerWidth < 768 && sidebar) {
            const sidebarLinks = sidebar.querySelectorAll('a, button[type="submit"]');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (!sidebar.classList.contains('translate-x-full')) {
                        toggleSidebar();
                    }
                });
            });
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const successAlert = document.getElementById('flash-alert');
            const errorAlert = document.getElementById('flash-alert-error');
            
            function autoDismiss(alert) {
                if (!alert) return;
                setTimeout(() => {
                    if (window.Alpine && alert.hasAttribute('x-data')) {
                        Alpine.$data(alert).show = false;
                        setTimeout(() => alert.remove(), 300);
                    } else {
                        alert.style.transition = 'opacity 0.3s ease-in';
                        alert.style.opacity = '0';
                        setTimeout(() => alert.remove(), 300);
                    }
                }, 4000);
            }
            
            if (window.Alpine) {
                autoDismiss(successAlert);
                autoDismiss(errorAlert);
            } else {
                document.addEventListener('alpine:init', () => {
                    autoDismiss(successAlert);
                    autoDismiss(errorAlert);
                });
                setTimeout(() => {
                    autoDismiss(successAlert);
                    autoDismiss(errorAlert);
                }, 100);
            }
        });
    </script>

    <script>
        // Define these globally so they can be used anywhere
        window.showConfirmModal = null;
        window.confirmDelete = null;

        document.addEventListener('DOMContentLoaded', function () {
            const confirmModal = document.getElementById('site-confirm-modal');
            if (!confirmModal) return;

            const modalBackdrop = document.getElementById('site-confirm-backdrop');
            const closeBtn = document.getElementById('site-confirm-close');
            const cancelBtn = document.getElementById('site-confirm-cancel');
            const okBtn = document.getElementById('site-confirm-ok');
            const msgEl = document.getElementById('site-confirm-message');
            
            // Input elements for "Type to confirm" (optional usage)
            const inputWrapper = document.getElementById('site-confirm-input-wrapper');
            const inputEl = document.getElementById('site-confirm-input');
            const helpEl = document.getElementById('site-confirm-help');
            const errorEl = document.getElementById('site-confirm-error');

            let currentForm = null;
            let expectedInput = null;

            // 1. Show Modal Function
            window.showConfirmModal = function(message, form, expectedText = null, placeholder = null, okText = 'تأكيد') {
                currentForm = form;
                expectedInput = expectedText;
                
                // Format message (handle newlines)
                if(msgEl) msgEl.innerHTML = message.replace(/\n/g, '<br>');
                if(okBtn) okBtn.textContent = okText;

                // Handle text input confirmation if needed
                if (expectedText && inputWrapper) {
                    inputWrapper.classList.remove('hidden');
                    inputEl.value = '';
                    inputEl.placeholder = placeholder || 'اكتب كلمة التأكيد';
                    if (helpEl) helpEl.innerHTML = `اكتب <strong>${expectedText}</strong> للتأكيد`;
                    if (errorEl) errorEl.classList.add('hidden');
                } else if (inputWrapper) {
                    inputWrapper.classList.add('hidden');
                }

                confirmModal.classList.remove('hidden');
                confirmModal.classList.add('flex');
            };

            // 2. Hide Modal Function
            function hideConfirmModal() {
                confirmModal.classList.add('hidden');
                confirmModal.classList.remove('flex');
                currentForm = null;
                expectedInput = null;
                if (inputEl) inputEl.value = '';
            }

            // 3. Handle Confirmation Click
            function handleConfirm() {
                // Verification logic
                if (expectedInput && inputEl) {
                    if (inputEl.value.trim() !== expectedInput) {
                        if (errorEl) {
                            errorEl.textContent = `لم يتم التأكيد بشكل صحيح.`;
                            errorEl.classList.remove('hidden');
                        }
                        return;
                    }
                }

                // SUBMIT THE FORM
                if (currentForm) {
                    // We use submit() method which bypasses onsubmit handler to avoid infinite loop
                    currentForm.submit();
                }
                hideConfirmModal();
            }

            // 4. Global Helper specifically for Delete Forms (onsubmit="return confirmDelete(...)")
            window.confirmDelete = function(form, message = 'هل أنت متأكد من حذف هذا العنصر نهائياً؟') {
                // Open the modal
                window.showConfirmModal(message, form, null, null, 'حذف');
                
                // RETURN FALSE to stop the form from submitting immediately
                // The form will be submitted programmatically inside handleConfirm()
                return false;
            };

            // Event Listeners
            modalBackdrop?.addEventListener('click', hideConfirmModal);
            closeBtn?.addEventListener('click', hideConfirmModal);
            cancelBtn?.addEventListener('click', hideConfirmModal);
            okBtn?.addEventListener('click', handleConfirm);
            
            // Allow Escape key to close
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !confirmModal.classList.contains('hidden')) {
                    hideConfirmModal();
                }
            });

            // Delegate clicks to .js-confirm buttons (for edit pages)
            document.addEventListener('click', function(e) {
                const confirmBtn = e.target.closest('.js-confirm');
                if (!confirmBtn) return;

                e.preventDefault();
                e.stopPropagation();

                const form = confirmBtn.closest('form');
                if (!form) {
                    console.warn('js-confirm button must be inside a form element');
                    return;
                }

                const message = confirmBtn.getAttribute('data-confirm-message') || 'هل أنت متأكد من تنفيذ هذا الإجراء؟';
                const expectedInput = confirmBtn.getAttribute('data-confirm-input') || null;
                const placeholder = confirmBtn.getAttribute('data-confirm-input-placeholder') || null;
                const okText = confirmBtn.getAttribute('data-confirm-ok-text') || 'تأكيد';

                window.showConfirmModal(message, form, expectedInput, placeholder, okText);
            });
        });
    </script>
</body>
</html>

