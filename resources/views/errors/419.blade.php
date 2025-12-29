@extends('errors.layout')

@section('code', '419')

@section('title', 'انتهت صلاحية الصفحة')

@section('message')
    انتهت صلاحية الجلسة الخاصة بك. يرجى تحديث الصفحة والمحاولة مرة أخرى. هذا إجراء أمني لحماية بياناتك.
@endsection

@section('icon')
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
@endsection

