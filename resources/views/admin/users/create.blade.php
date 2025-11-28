@extends('layouts.admin')

@section('title', 'إنشاء مستخدم جديد')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <h1 class="text-3xl font-serif font-bold text-brand-primary">إنشاء مستخدم جديد</h1>
    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors bg-white">
        العودة للقائمة
    </a>
</div>

<form action="{{ route('admin.users.store') }}" method="POST" id="user-form">
    @csrf
    
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Main Column (Content) -->
        <div class="w-full lg:w-2/3 space-y-6">
            
            <!-- Basic Information -->
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">المعلومات الأساسية</h3>
                
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">الاسم الكامل</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-accent @error('name') border-red-500 @enderror"
                               placeholder="أدخل الاسم الكامل">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-accent @error('email') border-red-500 @enderror"
                               placeholder="example@domain.com"
                               dir="ltr">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Password Section -->
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">كلمة المرور</h3>
                
                <div class="space-y-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور</label>
                        <input type="password" name="password" id="password" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-accent @error('password') border-red-500 @enderror"
                               placeholder="••••••••">
                        <p class="mt-1 text-xs text-gray-500">اتركها فارغة لتوليد كلمة مرور عشوائية آمنة (12 حرف)</p>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">تأكيد كلمة المرور</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-accent"
                               placeholder="••••••••">
                    </div>
                </div>
            </div>

        </div>

        <!-- Sidebar Column (Settings) -->
        <div class="w-full lg:w-1/3 space-y-6">
            
            <!-- Role & Permissions -->
            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">الصلاحيات</h3>
                
                <div class="space-y-4">
                    @if(auth()->user()->isSuperAdmin())
                    <label class="flex items-start cursor-pointer hover:bg-gray-50 p-3 rounded transition-colors">
                        <input type="checkbox" name="is_super_admin" value="1" {{ old('is_super_admin') ? 'checked' : '' }} 
                               class="rounded border-gray-300 text-purple-600 focus:ring-purple-500 h-5 w-5 mt-0.5">
                        <div class="mr-3">
                            <span class="block text-sm font-medium text-gray-700">مدير النظام</span>
                            <span class="block text-xs text-gray-500">صلاحيات كاملة بما في ذلك إدارة المستخدمين</span>
                        </div>
                    </label>
                    @endif
                    
                    <label class="flex items-start cursor-pointer hover:bg-gray-50 p-3 rounded transition-colors">
                        <input type="checkbox" name="is_admin" value="1" {{ old('is_admin', true) ? 'checked' : '' }} 
                               class="rounded border-gray-300 text-brand-accent focus:ring-brand-accent h-5 w-5 mt-0.5">
                        <div class="mr-3">
                            <span class="block text-sm font-medium text-gray-700">مشرف</span>
                            <span class="block text-xs text-gray-500">يمكنه إدارة المحتوى (المقالات، الأقسام، الوسوم)</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Publishing Actions -->
            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">الإجراءات</h3>

                <div class="flex items-center justify-between pt-4 border-t mt-4">
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors text-sm font-medium">
                        إلغاء
                    </a>
                    <button type="submit" class="px-6 py-2 bg-brand-primary text-white rounded hover:bg-opacity-90 transition-colors text-sm font-medium shadow-sm">
                        إنشاء المستخدم
                    </button>
                </div>
            </div>

            <!-- Info Card -->
            <div class="bg-amber-50 border border-amber-200 p-4 rounded shadow-sm">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-amber-600 ml-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="text-sm text-amber-800">
                        <p class="font-semibold mb-1">ملاحظة هامة</p>
                        <p class="text-xs">إذا تركت كلمة المرور فارغة، سيتم إنشاء كلمة مرور عشوائية آمنة (12 حرف).</p>
                    </div>
                </div>
            </div>

            <!-- User Roles Info -->
            <div class="bg-white p-4 rounded shadow">
                <h3 class="font-bold text-gray-800 mb-3 text-sm">أنواع المستخدمين</h3>
                <div class="space-y-3">
                    @if(auth()->user()->isSuperAdmin())
                    <div class="flex items-start">
                        <div class="w-2 h-2 rounded-full bg-purple-600 mt-1.5 ml-2 flex-shrink-0"></div>
                        <div>
                            <p class="text-xs font-medium text-gray-700">مدير النظام</p>
                            <p class="text-xs text-gray-500">صلاحيات كاملة بما في ذلك إدارة المستخدمين</p>
                        </div>
                    </div>
                    @endif
                    <div class="flex items-start">
                        <div class="w-2 h-2 rounded-full bg-brand-accent mt-1.5 ml-2 flex-shrink-0"></div>
                        <div>
                            <p class="text-xs font-medium text-gray-700">مشرف</p>
                            <p class="text-xs text-gray-500">يمكنه إدارة المحتوى (المقالات، الأقسام، الوسوم، الوسائط)</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    // Password strength indicator (optional enhancement)
    const passwordInput = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirmation');
    
    // Simple password match validation
    passwordConfirm.addEventListener('input', function() {
        if (passwordInput.value && this.value) {
            if (passwordInput.value === this.value) {
                this.classList.remove('border-red-500');
                this.classList.add('border-green-500');
            } else {
                this.classList.remove('border-green-500');
                this.classList.add('border-red-500');
            }
        }
    });
</script>
@endpush
