<x-app-layout>
    <div class="py-12" dir="rtl">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Column: Profile Card -->
                <div class="space-y-6">
                    <!-- Profile Info Card -->
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <div class="p-6 flex flex-col items-center text-center border-b border-gray-100">
                            <div class="w-24 h-24 bg-gray-200 rounded-full mb-4 overflow-hidden shadow-inner">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&size=128" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-500 mb-2">{{ $user->email }}</p>
                            
                            @if($user->is_super_admin)
                                <span class="px-3 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full">مدير النظام</span>
                            @elseif($user->is_admin)
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">مسؤول</span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">مستخدم</span>
                            @endif
                        </div>
                        
                        <div class="p-6 bg-gray-50">
                            <h4 class="text-sm font-bold text-gray-700 mb-2 border-b pb-2 border-gray-200">النبذة القصيرة</h4>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                {{ $user->short_bio ?? 'لم تتم إضافة نبذة قصيرة بعد.' }}
                            </p>
                </div>
            </div>

                    <!-- Quick Actions (Optional sidebar content) -->
                    <div class="bg-white shadow-md rounded-lg p-6">
                        <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">معلومات الحساب</h3>
                        <ul class="space-y-3 text-sm text-gray-600">
                            <li class="flex justify-between">
                                <span>تاريخ التسجيل:</span>
                                <span class="font-medium">{{ $user->created_at->format('Y/m/d') }}</span>
                            </li>
                            <li class="flex justify-between">
                                <span>آخر تحديث:</span>
                                <span class="font-medium">{{ $user->updated_at->format('Y/m/d') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Right Column: Forms -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Consolidated Profile Information Form -->
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-lg font-bold text-gray-800">تعديل البيانات الشخصية</h3>
                        </div>
                        <div class="p-6">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <!-- Password Update Form -->
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-lg font-bold text-gray-800">تحديث كلمة المرور</h3>
                        </div>
                        <div class="p-6">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <!-- Delete Account Zone -->
                    <div class="bg-white shadow-md rounded-lg overflow-hidden border border-red-100">
                        <div class="px-6 py-4 border-b border-red-100 bg-red-50">
                            <h3 class="text-lg font-bold text-red-800">منطقة الخطر</h3>
                        </div>
                        <div class="p-6">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
