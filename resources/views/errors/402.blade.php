@extends('errors.layout')

@section('code', '402')

@section('title', 'الدفع مطلوب')

@section('message')
    عذراً، يتطلب الوصول إلى هذا المحتوى اشتراكاً مدفوعاً أو إكمال عملية الدفع. يرجى التواصل معنا للمزيد من المعلومات.
@endsection

@section('icon')
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
    </svg>
@endsection

