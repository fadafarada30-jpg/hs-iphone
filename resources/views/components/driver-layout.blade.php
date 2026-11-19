<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'Jadwal COD' }} · HS IPHONE DRIVER</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23111111'/><text x='50' y='68' font-family='system-ui, -apple-system, sans-serif' font-weight='700' font-size='52' fill='%23ffffff' text-anchor='middle'>HS</text></svg>">
    @vite(['resources/css/app.css', 'resources/css/driver.css', 'resources/js/app.js'])
</head>
<body class="driver-shell">
@if(session('success'))
    <div data-flash="{{ session('success') }}" data-flash-type="success" hidden></div>
@endif
@if(session('error'))
    <div data-flash="{{ session('error') }}" data-flash-type="error" hidden></div>
@endif

<div class="driver-shell">
    <header class="driver-topbar">
        <div class="driver-topbar-inner">
            <a href="{{ route('driver.index') }}" class="driver-brand">
                <span class="driver-brand-mark">HS</span>
                <span>Driver COD<small>jadwal &amp; penyerahan unit</small></span>
            </a>

            <div class="driver-topbar-right">
                <span class="driver-role">Driver COD</span>
                <span class="driver-avatar">{{ auth()->user()->initials() }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="driver-logout">Keluar</button>
                </form>
            </div>
        </div>
    </header>

    <main class="driver-main">
        {{ $slot }}
    </main>
</div>

<div id="toast-container"></div>
</body>
</html>
