<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('معلومات الملف الشخصي') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("تحديث معلومات حسابك والبريد الإلكتروني.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" id="profile-form">
        @csrf
        @method('patch')

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('الاسم')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('البريد الإلكتروني')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('عنوان بريدك الإلكتروني غير مؤكد.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('انقر هنا لإعادة إرسال رسالة التحقق.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('تم إرسال رابط تحقق جديد إلى عنوان بريدك الإلكتروني.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Short Bio -->
        <div>
            <x-input-label for="short_bio" :value="__('النبذة القصيرة')" />
            <textarea id="short_bio" name="short_bio" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3" placeholder="نبذة مختصرة تظهر في بطاقة المؤلف">{{ old('short_bio', $user->short_bio) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('short_bio')" />
        </div>

        <!-- Detailed Biography (Super Admin Only) -->
        @if(auth()->user()->is_super_admin || auth()->id() === 1)
        <div>
            <x-input-label for="biography" :value="__('النبذة التعريفية')" />
            <p class="text-xs text-gray-500 mb-2">اكتب نبذة تفصيلية عنك تظهر في صفحة "عني"</p>
            <textarea id="biography" name="biography" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="8">{{ old('biography', $user->biography) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('biography')" />
        </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('حفظ التغييرات') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('تم الحفظ.') }}</p>
            @endif
        </div>
    </form>
</section>

@push('styles')
<style>
    /* CKEditor Styling */
    #biography {
        min-height: 200px;
    }
    .ck-editor__editable {
        min-height: 400px !important;
        max-height: 70vh !important;
        overflow-y: auto !important;
        padding: 20px !important;
        line-height: 1.8 !important;
        font-size: 16px !important;
    }
    
    /* Professional Editor Styles */
    .ck-content {
        font-family: 'Cairo', sans-serif;
    }
    
    .ck-content ul {
        list-style-type: disc;
        padding-right: 20px;
    }
    .ck-content ol {
        list-style-type: decimal;
        padding-right: 20px;
    }
    .ck-content h2 {
        font-size: 1.5em;
        font-weight: bold;
        margin: 1em 0;
    }
    .ck-content h3 {
        font-size: 1.17em;
        font-weight: bold;
        margin: 0.8em 0;
    }
    .ck-content h4 {
        font-size: 1em;
        font-weight: bold;
        margin: 0.8em 0;
    }
    .ck-content p {
        margin-bottom: 0.8em;
    }
</style>
@endpush

@push('scripts')
<!-- CKEditor 5 Classic Build CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js" onerror="console.warn('CKEditor CDN failed to load. Will attempt local fallback if available.');"></script>

<script>
    // CKEditor 5 Initialization with Arabic/RTL Support - Robust with fallback
    document.addEventListener('DOMContentLoaded', function () {
        const target = document.querySelector('#biography');
        
        // Check if biography textarea exists (only for super admin)
        if (!target) {
            console.warn('CKEditor init: #biography not found. CKEditor will not be initialized on this page. (This is normal if you are not a super admin.)');
            return;
        }

        // Function to initialize the editor
        function initEditor() {
            if (typeof ClassicEditor === 'undefined') {
                console.error('CKEditor library is not loaded (ClassicEditor is undefined). Trying local fallback...');
                
                // Try to load a local fallback script if exists
                const localScriptUrl = '/js/ckeditor-local.js';
                const script = document.createElement('script');
                script.src = localScriptUrl;
                script.onerror = function() {
                    console.error('Local CKEditor fallback not found at', localScriptUrl);
                    console.error('CKEditor initialization failed. Please ensure CKEditor is loaded via CDN or local file.');
                };
                script.onload = function() {
                    console.info('Local CKEditor fallback loaded successfully.');
                    if (typeof ClassicEditor !== 'undefined') {
                        createEditor();
                    }
                };
                document.body.appendChild(script);
                return;
            }

            createEditor();
        }

        // Function to create the editor instance
        function createEditor() {
            ClassicEditor.create(target, {
                language: { ui: 'ar', content: 'ar' },
                placeholder: 'اكتب نبذة تفصيلية هنا...',
                toolbar: {
                    items: [
                        'heading', '|',
                        'bold', 'italic', 'underline', '|',
                        'link', 'bulletedList', 'numberedList', '|',
                        'blockQuote', 'insertTable', '|',
                        'undo', 'redo'
                    ]
                }
            })
            .then(editor => { 
                window.biographyEditor = editor;
                console.info('CKEditor initialized successfully for #biography.');
                
                // Ensure editor content syncs to textarea on form submit
                const form = document.getElementById('profile-form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        try {
                            // Update textarea with editor content before submission
                            editor.updateSourceElement();
                            console.debug('CKEditor content synced to textarea before form submission.');
                        } catch(syncError) {
                            console.warn('CKEditor sync error (non-critical):', syncError);
                            // Continue with form submission even if sync fails
                        }
                    });
                } else {
                    console.warn('Profile form (#profile-form) not found. CKEditor sync on submit may not work.');
                }
            })
            .catch(error => { 
                console.error('CKEditor creation error:', error);
                console.error('CKEditor failed to initialize. The textarea will remain as a plain textarea.');
            });
        }

        // Wait a bit for CDN script to load, then initialize
        if (typeof ClassicEditor !== 'undefined') {
            // ClassicEditor is already available, initialize immediately
            initEditor();
        } else {
            // Wait for script to load (max 3 seconds)
            let attempts = 0;
            const maxAttempts = 30; // 30 * 100ms = 3 seconds
            const checkInterval = setInterval(function() {
                attempts++;
                if (typeof ClassicEditor !== 'undefined') {
                    clearInterval(checkInterval);
                    initEditor();
                } else if (attempts >= maxAttempts) {
                    clearInterval(checkInterval);
                    console.warn('CKEditor ClassicEditor not available after waiting. Attempting fallback...');
                    initEditor(); // This will trigger the fallback logic
                }
            }, 100);
        }
    });
</script>
@endpush
