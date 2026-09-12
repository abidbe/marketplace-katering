<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    @fonts

    <script>
        document.documentElement.dataset.theme = localStorage.getItem('theme') ?? 'light';
    </script>

    @vite(['resources/css/app.css', 'resources/css/custom.css', 'resources/js/app.js', 'resources/js/main.js'])
</head>
<body class="min-h-screen bg-base-100 font-sans antialiased">
    <header class="navbar bg-base-200 shadow-sm sticky top-0 z-40 px-4">
        <div class="navbar-start">
            <a href="{{ url('/') }}" class="btn btn-ghost text-lg font-bold px-2">
                {{ config('app.name', 'Laravel') }}
            </a>
        </div>

        <div class="navbar-end">
            <label class="swap swap-rotate btn btn-ghost btn-circle">
                <input type="checkbox" class="swap-input theme-controller" aria-label="Ganti tema" />

                <i class="swap-off ri-sun-line text-xl"></i>

                <i class="swap-on ri-moon-line text-xl"></i>
            </label>
        </div>
    </header>

    <main class="min-h-[calc(100vh-4rem)]">
        @yield('content')
    </main>

    @include('components.alert')
</body>
</html>
