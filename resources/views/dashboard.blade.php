@extends('layouts.main')
@section('breadcrumb')
    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
        Dashboard
    </a>
@endsection
@section('pages')
    <div class="p-6">
        Selamat Datang, {{ Auth::user()->name }}
    </div>
@endsection
