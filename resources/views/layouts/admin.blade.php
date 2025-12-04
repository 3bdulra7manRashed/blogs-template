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
    {{-- Centralized modal flash (global) --}}
    @php
        $success = session('success');
        $error = session('error');
        $plainPassword = session()->pull('plain_password'); // consumed once
    @endphp

    <div id="site-flash-modal" aria-hidden="true" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <!-- backdrop -->
        <div id="site-flash-backdrop" class="absolute inset-0 bg-black/60"></div>

        <!-- modal box -->
        <div role="dialog" aria-modal="true" aria-labelledby="site-flash-title"
             class="relative bg-white rounded-lg shadow-2xl max-w-lg w-[90%] md:w-1/2 p-8 text-center">
            <button id="site-flash-close" aria-label="close"
                    class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>

            <div id="site-flash-icon" class="mx-auto mb-4 w-24 h-24 flex items-center justify-center rounded-full bg-green-50">
                <!-- icon will be replaced by JS -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h3 id="site-flash-title" class="text-2xl font-semibold text-gray-800 mb-2"></h3>

            <p id="site-flash-message" class="text-gray-600 mb-4"></p>

            <div id="site-flash-extra" class="text-gray-800 font-semibold mb-4"></div>

            <div class="flex justify-center">
                <button id="site-flash-ok" class="px-6 py-2 rounded bg-sky-300 hover:bg-sky-400 text-white">حسناً</button>
            </div>
        </div>
    </div>

    <!-- inject PHP variables into JS -->
    <script>
        window._siteFlash = {
            success: {!! json_encode($success ?? null) !!},
            error: {!! json_encode($error ?? null) !!},
            plainPassword: {!! json_encode($plainPassword ?? null) !!}
        };
    </script>

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
                {{-- flash alerts centralized to layouts.admin (modal) --}}
                @yield('content')
            </div>
        </main>
    </div>
    
    @stack('scripts')
    
    <!-- Mobile Menu Toggle Script -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        
        function toggleSidebar() {
            sidebar.classList.toggle('translate-x-full');
            backdrop.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
        }
        
        mobileMenuBtn?.addEventListener('click', toggleSidebar);
        backdrop?.addEventListener('click', toggleSidebar);
        
        // Close sidebar when clicking a link on mobile
        if (window.innerWidth < 768) {
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

    <!-- Global Flash Modal Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const payload = window._siteFlash || {};
            const success = payload.success || null;
            const error = payload.error || null;
            const plainPassword = payload.plainPassword || null;

            const modal = document.getElementById('site-flash-modal');
            if (!modal) return;

            const backdrop = document.getElementById('site-flash-backdrop');
            const closeBtn = document.getElementById('site-flash-close');
            const okBtn = document.getElementById('site-flash-ok');
            const titleEl = document.getElementById('site-flash-title');
            const msgEl = document.getElementById('site-flash-message');
            const extraEl = document.getElementById('site-flash-extra');
            const iconEl = document.getElementById('site-flash-icon');

            function showModal(type, message, extraHtml = '') {
                if (type === 'success') {
                    titleEl.textContent = 'تم بنجاح !';
                    iconEl.className = 'mx-auto mb-4 w-24 h-24 flex items-center justify-center rounded-full bg-green-50';
                    iconEl.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>`;
                } else {
                    titleEl.textContent = 'حدث خطأ';
                    iconEl.className = 'mx-auto mb-4 w-24 h-24 flex items-center justify-center rounded-full bg-red-50';
                    iconEl.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>`;
                }

                // Safely render HTML and convert newlines to <br>
                if (message) {
                    // Convert newline characters to <br> tags
                    const safeHtml = String(message)
                        .replace(/\r\n/g, '\n')
                        .replace(/\r/g, '\n')
                        .replace(/\n/g, '<br>');
                    msgEl.innerHTML = safeHtml;
                } else {
                    msgEl.innerHTML = '';
                }
                extraEl.innerHTML = extraHtml || '';

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
                // focus OK button for accessibility
                okBtn.focus();
            }

            function hideModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }

            backdrop.addEventListener('click', hideModal);
            closeBtn.addEventListener('click', hideModal);
            okBtn.addEventListener('click', hideModal);
            document.addEventListener('keydown', function(e){
                if (e.key === 'Escape') hideModal();
            });

            if (success) {
                const extra = plainPassword ? `— كلمة المرور هي: <strong>${plainPassword}</strong> (تظهر مرة واحدة فقط)` : '';
                showModal('success', success, extra);
            } else if (error) {
                showModal('error', error);
            }
        });
    </script>

    <!-- Global Confirmation Modal Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const confirmModal = document.getElementById('site-confirm-modal');
            if (!confirmModal) return;

            const backdrop = document.getElementById('site-confirm-backdrop');
            const closeBtn = document.getElementById('site-confirm-close');
            const cancelBtn = document.getElementById('site-confirm-cancel');
            const okBtn = document.getElementById('site-confirm-ok');
            const titleEl = document.getElementById('site-confirm-title');
            const msgEl = document.getElementById('site-confirm-message');
            const inputWrapper = document.getElementById('site-confirm-input-wrapper');
            const inputEl = document.getElementById('site-confirm-input');
            const helpEl = document.getElementById('site-confirm-help');
            const errorEl = document.getElementById('site-confirm-error');

            let currentForm = null;
            let expectedInput = null;
            let inputPlaceholder = null;

            // Utility: escape HTML for user-provided fragments
            function escapeHtml(str) {
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            // Main: prepare and set modal message
            function prepareAndSetModalMessage(rawMessage) {
                if (!rawMessage) {
                    msgEl.innerHTML = '';
                    return;
                }

                // 1) Normalize line breaks
                let msg = String(rawMessage).replace(/\r\n/g, '\n').replace(/\r/g, '\n');

                // 2) Insert a newline after the FIRST Arabic question mark (؟) or Latin '?'
                //    This converts: "هل أنت متأكد؟ سيتم نقل..."  ->  "هل أنت متأكد؟\nسيتم نقل..."
                //    Handles both Arabic (U+061F) and Latin question marks
                msg = msg.replace(/[\u061F?](\s*)/, function(match) {
                    // Return the question mark followed by newline (trim trailing spaces)
                    return match.trim().slice(0, 1) + '\n';
                });

                // 3) Convert newlines to <br> for display
                //    We will escape everything first except the <br> that we insert, to avoid XSS.
                //    Split on '\n', escape each piece, then join with <br>.
                const parts = msg.split('\n');
                const escapedParts = parts.map(p => escapeHtml(p));
                const safeHtml = escapedParts.join('<br>');

                msgEl.innerHTML = safeHtml;
            }

            function showConfirmModal(message, form, expectedText = null, placeholder = null, okText = 'تأكيد') {
                currentForm = form;
                expectedInput = expectedText;
                inputPlaceholder = placeholder || 'اكتب كلمة التأكيد هنا';

                prepareAndSetModalMessage(message || 'هل أنت متأكد؟');
                okBtn.textContent = okText;

                if (expectedText) {
                    inputWrapper.classList.remove('hidden');
                    inputEl.value = '';
                    inputEl.placeholder = inputPlaceholder;
                    if (helpEl) {
                        helpEl.innerHTML = `اكتب <strong>${expectedText}</strong> للتأكيد`;
                    }
                    errorEl.classList.add('hidden');
                    inputEl.focus();
                } else {
                    inputWrapper.classList.add('hidden');
                    okBtn.focus();
                }

                confirmModal.classList.remove('hidden');
                confirmModal.classList.add('flex');
                confirmModal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }

            function hideConfirmModal() {
                confirmModal.classList.add('hidden');
                confirmModal.classList.remove('flex');
                confirmModal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
                currentForm = null;
                expectedInput = null;
                inputEl.value = '';
                errorEl.classList.add('hidden');
            }

            function handleConfirm() {
                if (expectedInput) {
                    const inputValue = inputEl.value.trim();
                    if (inputValue !== expectedInput) {
                        errorEl.textContent = `لم يتم التأكيد. اكتب "${expectedInput}" لتنفيذ الإجراء.`;
                        errorEl.classList.remove('hidden');
                        inputEl.focus();
                        return;
                    }
                }

                if (currentForm) {
                    currentForm.submit();
                } else {
                    console.warn('No form found for confirmation action');
                }
                hideConfirmModal();
            }

            // Event listeners
            backdrop.addEventListener('click', hideConfirmModal);
            closeBtn.addEventListener('click', hideConfirmModal);
            cancelBtn.addEventListener('click', hideConfirmModal);
            okBtn.addEventListener('click', handleConfirm);
            inputEl.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    handleConfirm();
                }
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !confirmModal.classList.contains('hidden')) {
                    hideConfirmModal();
                }
            });

            // Delegate clicks to .js-confirm buttons
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

                showConfirmModal(message, form, expectedInput, placeholder, okText);
            });
        });
    </script>
</body>
</html>

