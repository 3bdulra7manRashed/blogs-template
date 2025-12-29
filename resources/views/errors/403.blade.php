@extends('errors.layout')

@section('code', '403')

@section('title', 'الوصول محظور')

@section('message')
    {{ $exception->getMessage() ?: 'عذراً، ليس لديك صلاحية للوصول إلى هذه الصفحة. إذا كنت تعتقد أن هذا خطأ، يرجى التواصل مع الدعم الفني.' }}
@endsection

@section('icon')
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
    </svg>
@endsection

