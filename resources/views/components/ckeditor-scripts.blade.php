@once
<!-- CKEditor 5 Scripts -->
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
<script src="{{ asset('vendor/ckeditor/translations/ar.js') }}"></script>

<style>
    /* CKEditor Professional Styles */
    .ckeditor {
        min-height: 200px;
    }
    
    .ck-editor__editable {
        min-height: 700px !important;
        max-height: 90vh !important;
        overflow-y: auto !important;
        padding: 40px !important;
        line-height: 1.8 !important;
        font-size: 16px !important;
    }
    
    /* Professional Editor Styles - Like Word */
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Track initialized editors to prevent duplicate initialization
    const initializedEditors = new WeakSet();
    
    // Custom Image Upload Adapter
    class CustomImageUploadAdapter {
        constructor(loader) {
            this.loader = loader;
        }

        upload() {
            return this.loader.file
                .then(file => new Promise((resolve, reject) => {
                    const data = new FormData();
                    data.append('upload', file);
                    
                    // Get CSRF token from meta tag
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                    if (csrfToken) {
                        data.append('_token', csrfToken);
                    }

                    fetch('{{ route("ckeditor.upload") }}', { 
                        method: 'POST',
                        body: data,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken || ''
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.url) {
                            resolve({
                                default: data.url
                            });
                        } else {
                            reject(data.error || 'فشل عملية الرفع');
                        }
                    })
                    .catch(error => {
                        console.error('Upload error:', error);
                        reject('حدث خطأ أثناء رفع الصورة');
                    });
                }));
        }

        abort() {
            // Can add upload abort logic here if needed
        }
    }

    // Plugin to register the upload adapter
    function CustomImageUploadAdapterPlugin(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
            return new CustomImageUploadAdapter(loader);
        };
    }

    // Initialize all .ckeditor textareas
    function initializeCKEditors() {
        const textareas = document.querySelectorAll('textarea.ckeditor');
        
        textareas.forEach(textarea => {
            // Skip if already initialized
            if (initializedEditors.has(textarea)) {
                return;
            }
            
            // Mark as initialized
            initializedEditors.add(textarea);
            
            // Get configuration from data attributes
            const placeholder = textarea.dataset.placeholder || 'ابدأ الكتابة هنا...';
            const minHeight = textarea.dataset.minHeight || '700px';
            
            ClassicEditor.create(textarea, {
                language: { ui: 'ar', content: 'ar' },
                placeholder: placeholder,
                extraPlugins: [CustomImageUploadAdapterPlugin],
                toolbar: {
                    shouldNotGroupWhenFull: true,
                    items: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'link', 'blockQuote', 'insertTable', '|',
                        'bulletedList', 'numberedList', 'outdent', 'indent', '|',
                        'mediaEmbed', 'imageUpload', 'undo', 'redo'
                    ]
                },
                mediaEmbed: {
                    previewsInData: true
                }
            })
            .then(editor => {
                // Store editor instance on textarea element
                textarea.ckeditorInstance = editor;
                
                // Auto-sync content before form submission
                const form = textarea.closest('form');
                if (form && !form.ckeditorSyncAttached) {
                    form.ckeditorSyncAttached = true;
                    form.addEventListener('submit', function(e) {
                        // Sync all CKEditor instances in this form
                        const textareas = form.querySelectorAll('textarea.ckeditor');
                        textareas.forEach(ta => {
                            if (ta.ckeditorInstance) {
                                ta.ckeditorInstance.updateSourceElement();
                            }
                        });
                    });
                }
            })
            .catch(error => {
                console.error('CKEditor initialization error:', error);
                initializedEditors.delete(textarea); // Allow retry
            });
        });
    }
    
    // Initialize editors on page load
    initializeCKEditors();
    
    // Re-initialize if new editors are added dynamically (optional)
    // You can call window.initializeCKEditors() after adding new textareas via JS
    window.initializeCKEditors = initializeCKEditors;
});
</script>
@endonce

