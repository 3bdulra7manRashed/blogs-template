@extends('errors.layout')

@section('code', '429')

@section('title', 'طلبات كثيرة جداً')

@section('message')
    لقد قمت بإرسال طلبات كثيرة في وقت قصير. يرجى الانتظار قليلاً ثم المحاولة مرة أخرى. هذا إجراء لحماية الموقع.
@endsection

@section('icon')
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
    </svg>
@endsection

