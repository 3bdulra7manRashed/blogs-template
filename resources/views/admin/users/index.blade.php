@extends('layouts.admin')

@section('title', 'المستخدمين')

@section('content')
<div class="mb-6 flex items-center justify-between flex-wrap gap-4">
    <h1 class="text-3xl font-serif font-bold text-brand-primary">المستخدمين</h1>
    
    <div class="flex items-center gap-3">
        <div class="flex items-center gap-2 bg-white rounded-md shadow-sm p-1">
            <a href="{{ route('admin.users.index', ['status' => 'active']) }}" 
               class="px-3 py-1 text-xs rounded {{ request('status') === 'active' ? 'bg-brand-accent text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                نشط
            </a>
            <a href="{{ route('admin.users.index', ['status' => 'deleted']) }}" 
               class="px-3 py-1 text-xs rounded {{ request('status') === 'deleted' ? 'bg-brand-accent text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                محذوف
            </a>
            <a href="{{ route('admin.users.index') }}" 
               class="px-3 py-1 text-xs rounded {{ !request('status') ? 'bg-brand-accent text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                الكل
            </a>
        </div>

        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-accent text-white hover:bg-amber-700 hover:text-white rounded-md hover:bg-opacity-90 transition-colors">
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>مستخدم جديد</span>
        </a>
    </div>
</div>
{{-- flash alerts moved to layouts.admin --}}

<!-- Users Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 md:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
                    <th class="px-4 md:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">البريد الإلكتروني</th>
                    <th class="px-4 md:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">الأدوار</th>
                    <th class="px-4 md:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">تاريخ التسجيل</th>
                    <th class="px-4 md:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                    <th class="px-4 md:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors {{ $user->trashed() ? 'opacity-60 bg-gray-50' : '' }}">
                        <td class="px-4 md:px-6 py-4 text-center">
                            <div class="flex items-center justify-center">
                                <div class="flex-shrink-0 h-10 w-10 ml-3">
                                    <div class="h-10 w-10 rounded-full bg-brand-accent text-white flex items-center justify-center font-semibold text-sm">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="md:hidden text-xs text-gray-500 mt-1">
                                        @if($user->is_super_admin)
                                            <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                                مدير النظام
                                            </span>
                                        @elseif($user->roles->count() > 0)
                                            @foreach($user->roles as $role)
                                                <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full {{ $role->name === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                                    {{ $role->name === 'admin' ? 'مدير النظام' : ($role->name === 'moderator' ? 'مشرف' : $role->name) }}
                                                </span>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center font-mono hidden md:table-cell">
                            {{ $user->email }}
                        </td>
                        <td class="px-4 md:px-6 py-4 whitespace-nowrap text-center hidden md:table-cell">
                            @if($user->is_super_admin)
                                <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                    مدير النظام
                                </span>
                            @elseif($user->roles->count() > 0)
                                @foreach($user->roles as $role)
                                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full {{ $role->name === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $role->name === 'admin' ? 'مدير النظام' : ($role->name === 'moderator' ? 'مشرف' : $role->name) }}
                                    </span>
                                @endforeach
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">بدون دور</span>
                            @endif
                        </td>
                        <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center hidden lg:table-cell">
                            {{ $user->created_at->format('Y/m/d') }}
                        </td>
                        <td class="px-4 md:px-6 py-4 whitespace-nowrap text-center">
                            @if($user->trashed())
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    محذوف
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    نشط
                                </span>
                            @endif
                        </td>
                        <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            <div class="flex items-center gap-2 justify-center flex-wrap">
                                @if($user->trashed())
                                    {{-- Restore and Force Delete for soft-deleted users --}}
                                    @can('restore', $user)
                                        <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="js-confirm inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 hover:text-blue-800 font-medium text-xs transition-colors duration-200"
                                                    data-confirm-message="هل أنت متأكد من استعادة هذا المستخدم؟ ملاحظة: المقالات ستبقى مع حساب المستخدم المحذوف.">
                                                <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                                استعادة
                                            </button>
                                        </form>
                                    @endcan
                                    
                                    @can('forceDelete', $user)
                                        @if($user->id !== 1)
                                            <form action="{{ route('admin.users.forceDelete', $user->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="js-confirm inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 rounded-md hover:bg-red-100 hover:text-red-800 font-medium text-xs transition-colors duration-200"
                                                        data-confirm-message="تحذير: هل أنت متأكد من الحذف النهائي؟ هذا الإجراء لا يمكن التراجع عنه!"
                                                        data-confirm-input="حذف"
                                                        data-confirm-input-placeholder="اكتب 'حذف' للتأكيد">
                                                    <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    حذف نهائي
                                                </button>
                                            </form>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-400 rounded-md font-medium text-xs">محمي</span>
                                        @endif
                                    @endcan
                                @else
                                    {{-- Promote/Demote and Soft Delete for active users --}}
                                @if(!$user->hasRole('admin'))
                                    <form action="{{ route('admin.users.promote', $user) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="js-confirm inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 rounded-md hover:bg-green-100 hover:text-green-800 font-medium text-xs transition-colors duration-200"
                                                data-confirm-message="هل أنت متأكد من ترقية هذا المستخدم إلى مشرف؟">
                                            <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                            </svg>
                                            ترقية
                                        </button>
                                    </form>
                                @else
                                    @if($user->id === auth()->id() || $user->email === 'admin@example.com')
                                        <span class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-400 rounded-md font-medium text-xs">محمي</span>
                                    @else
                                        <form action="{{ route('admin.users.demote', $user) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="js-confirm inline-flex items-center px-3 py-1.5 bg-orange-50 text-orange-700 rounded-md hover:bg-orange-100 hover:text-orange-800 font-medium text-xs transition-colors duration-200"
                                                    data-confirm-message="هل أنت متأكد من إزالة دور المشرف؟">
                                                <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                                </svg>
                                                إزالة
                                            </button>
                                        </form>
                                    @endif
                                @endif
                                    
                                @can('delete', $user)
                                    @if($user->id !== 1 && $user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="js-confirm inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 rounded-md hover:bg-red-100 hover:text-red-800 font-medium text-xs transition-colors duration-200"
                                                    data-confirm-message="هل أنت متأكد من حذف هذا المستخدم؟ سيتم نقل جميع مقالاته إلى حساب المستخدم المحذوف.">
                                                <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                حذف
                                            </button>
                                        </form>
                                    @elseif($user->id === 1)
                                        <span class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-400 rounded-md font-medium text-xs">محمي</span>
                                    @endif
                                @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 md:px-6 py-8 text-center text-gray-500">
                            لا يوجد مستخدمين
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if($users->hasPages())
    <div class="mt-6" dir="ltr">
        {{ $users->links() }}
    </div>
@endif

{{-- confirmation modal centralized to layouts.admin --}}
@endsection
