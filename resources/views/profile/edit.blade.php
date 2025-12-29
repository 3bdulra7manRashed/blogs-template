<x-app-layout>
    <div class="py-12" dir="rtl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                
                <!-- Mobile: Minimal Header (Avatar + Name) -->
                <div class="md:hidden order-1">
                    <div class="bg-white shadow-md rounded-lg p-4 flex items-center gap-4">
                        <div class="w-16 h-16 bg-gray-200 rounded-full overflow-hidden shadow-inner flex-shrink-0">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&size=128" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-bold text-gray-900 truncate">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                            @if($user->is_super_admin)
                                <span class="inline-block mt-1 px-2 py-0.5 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full">مدير النظام</span>
                            @elseif($user->is_admin)
                                <span class="inline-block mt-1 px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">مسؤول</span>
                            @else
                                <span class="inline-block mt-1 px-2 py-0.5 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">مستخدم</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column: Forms (Desktop: Right, Mobile: Top) - First in grid for RTL -->
                <div class="md:col-span-8 space-y-6 order-2 md:order-none">
                    
                    <!-- Consolidated Profile Information Form -->
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <div class="px-4 md:px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-lg font-bold text-gray-800">تعديل البيانات الشخصية</h3>
                        </div>
                        <div class="p-4 md:p-6">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <!-- Password Update Form -->
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <div class="px-4 md:px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-lg font-bold text-gray-800">تحديث كلمة المرور</h3>
                        </div>
                        <div class="p-4 md:p-6">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <!-- Delete Account Zone -->
                    <div class="bg-white shadow-md rounded-lg overflow-hidden border border-red-100">
                        <div class="px-4 md:px-6 py-4 border-b border-red-100 bg-red-50">
                            <h3 class="text-lg font-bold text-red-800">منطقة الخطر</h3>
                        </div>
                        <div class="p-4 md:p-6">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>

                </div>

                <!-- Left Column: Profile Card (Desktop: Left Sidebar, Hidden on Mobile) - Second in grid for RTL -->
                <div class="hidden md:block md:col-span-4 space-y-6 md:order-1">
                    <!-- Profile Info Card -->
                    <div class="bg-white shadow-md rounded-lg overflow-hidden md:sticky md:top-4">
                        <div class="p-4 md:p-6 flex flex-col items-center text-center border-b border-gray-100">
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
                        
                        <!-- Account Information -->
                        <div class="p-4 md:p-6">
                            <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 text-sm md:text-base">معلومات الحساب</h3>
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
                </div>

            </div>
        </div>
    </div>

    <!-- CKEditor Scripts -->
    @ckeditorScripts

    <!-- Mobile CKEditor Styles -->
    <style>
        @media (max-width: 768px) {
            .ck-editor__editable {
                min-height: 250px !important;
            }
            .ck-toolbar {
                flex-wrap: wrap;
            }
        }
    </style>

    <!-- Additional Profile Form Sync Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profileForm = document.getElementById('profile-form');
            
            if (profileForm) {
                // Add submit handler with higher priority
                profileForm.addEventListener('submit', function(e) {
                    // Find biography textarea
                    const biographyTextarea = document.querySelector('textarea[name="biography"]');
                    
                    if (biographyTextarea && biographyTextarea.ckeditorInstance) {
                        // Sync editor content to textarea
                        const editorData = biographyTextarea.ckeditorInstance.getData();
                        biographyTextarea.value = editorData;
                        
                        console.log('Biography synced:', editorData.substring(0, 100) + '...');
                    }
                }, true); // Use capture phase for earlier execution
            }
        });
    </script>
</x-app-layout>
