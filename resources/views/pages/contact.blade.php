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

                <form action="{{ route('contact.send') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-bold text-gray-700">الاسم الكامل</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-accent/20 focus:border-brand-accent transition-all outline-none bg-gray-50 focus:bg-white placeholder-gray-400"
                                   placeholder="أدخل اسمك هنا">
                            @error('name')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-bold text-gray-700">البريد الإلكتروني</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-accent/20 focus:border-brand-accent transition-all outline-none bg-gray-50 focus:bg-white placeholder-gray-400"
                                   placeholder="name@example.com">
                            @error('email')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="space-y-2">
                        <label for="message" class="block text-sm font-bold text-gray-700">نص الرسالة</label>
                        <textarea name="message" id="message" rows="6" required 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-brand-accent/20 focus:border-brand-accent transition-all outline-none bg-gray-50 focus:bg-white placeholder-gray-400 resize-none"
                                  placeholder="كيف يمكننا مساعدتك؟">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4 text-left">
                        <button type="submit" class="inline-flex items-center justify-center px-8 py-3.5 bg-brand-primary text-white font-bold rounded-xl hover:bg-brand-accent transition-all transform hover:-translate-y-0.5 shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-accent">
                            <span>إرسال الرسالة</span>
                            <svg class="w-5 h-5 mr-2 -ml-1 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
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
