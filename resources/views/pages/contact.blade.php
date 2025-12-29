@extends('layouts.blog')

@section('title', 'تواصل معي - ' . config('app.name'))

@section('content')
<div class="container mx-auto px-4 py-16" dir="rtl">
    
    <!-- Header Section -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-serif font-bold text-brand-primary mb-4">تواصل معي</h1>
        <p class="text-gray-500 text-lg max-w-2xl mx-auto">
            أسعد دائماً بالاستماع إلى آرائكم واقتراحاتكم. لا تتردد في مراسلتي في أي وقت.
        </p>
    </div>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100 flex flex-col md:flex-row">
            
            <!-- Contact Form Section -->
            <div class="w-full p-8 md:p-12">
                @if(session('success'))
                    <div class="mb-8 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3 text-green-800">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                <form action="{{ route('contact.send') }}" method="POST" class="space-y-6" id="contact-form">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-bold text-gray-700">الاسم</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" 
                                   class="w-full px-4 py-3 border rounded-xl transition-all outline-none bg-gray-50 focus:bg-white placeholder-gray-400 @error('name') border-red-500 focus:ring-2 focus:ring-red-200 @else border-gray-300 focus:ring-2 focus:ring-brand-accent focus:border-brand-accent @enderror"
                                   placeholder="أدخل اسمك">
                            @error('name')
                                <p class="text-sm text-red-600 mt-1 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-bold text-gray-700">البريد الإلكتروني</label>
                            <input type="text" name="email" id="email" value="{{ old('email') }}" 
                                   class="w-full px-4 py-3 border rounded-xl transition-all outline-none bg-gray-50 focus:bg-white placeholder-gray-400 @error('email') border-red-500 focus:ring-2 focus:ring-red-200 @else border-gray-300 focus:ring-2 focus:ring-brand-accent focus:border-brand-accent @enderror"
                                   placeholder="name@example.com"
                                   dir="ltr">
                            @error('email')
                                <p class="text-sm text-red-600 mt-1 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="space-y-2">
                        <label for="message" class="block text-sm font-bold text-gray-700">نص الرسالة</label>
                        <textarea name="message" id="message" rows="6" 
                                  class="w-full px-4 py-3 border rounded-xl transition-all outline-none bg-gray-50 focus:bg-white placeholder-gray-400 resize-none @error('message') border-red-500 focus:ring-2 focus:ring-red-200 @else border-gray-300 focus:ring-2 focus:ring-brand-accent focus:border-brand-accent @enderror"
                                  placeholder="اكتب رسالتك هنا...">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-sm text-red-600 mt-1 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- reCAPTCHA -->
                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-gray-700">التحقق الأمني</label>
                        <div class="@error('g-recaptcha-response') border-2 border-red-500 rounded-lg p-2 @enderror" style="display: inline-block;">
                            @if(config('services.recaptcha.site_key'))
                                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                            @else
                                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-800 text-sm">
                                    ⚠️ reCAPTCHA غير مفعّل. يرجى إضافة RECAPTCHA_SITE_KEY في ملف .env
                                </div>
                            @endif
                        </div>
                        @error('g-recaptcha-response')
                            <p class="text-sm text-red-600 mt-1 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4 text-right">
                        <button type="submit" class="submit-btn inline-flex items-center justify-center px-8 py-3.5 text-white font-bold rounded-xl transition-all transform hover:-translate-y-0.5 shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-accent" style="background-color: #c37c54;">
                            <span>إرسال الرسالة</span>
                            <svg class="w-5 h-5 mr-2 -ml-1 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                        <style>
                            .submit-btn:hover {
                                background-color: #a86845 !important;
                            }
                        </style>
                    </div>
                </form>
            </div>

        </div>
        
        <!-- Optional: Simple footer for contact page -->
        <div class="mt-8 text-center text-gray-500 text-sm">
            <p>يمكنك أيضاً التواصل معنا عبر البريد الإلكتروني المباشر: <a href="mailto:info@example.com" class="text-brand-accent hover:underline font-medium">info@example.com</a></p>
        </div>
    </div>
</div>
@endsection

@if(config('services.recaptcha.site_key'))
@push('scripts')
<!-- reCAPTCHA v2 (visible checkbox) -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush
@endif
