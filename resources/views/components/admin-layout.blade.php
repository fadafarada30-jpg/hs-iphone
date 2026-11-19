<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'Admin' }} · HS IPHONE CONSOLE</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>H</text></svg>">
    @vite(['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js'])
</head>
<body class="admin-shell">
@if(session('success'))
    <div data-flash="{{ session('success') }}" data-flash-type="success" hidden></div>
@endif
@if(session('error'))
    <div data-flash="{{ session('error') }}" data-flash-type="error" hidden></div>
@endif

<div class="admin-shell">
    <aside class="admin-sidebar">
        <a href="{{ route('admin.dashboard') }}" class="admin-brand">
            <span class="admin-brand-mark">HS</span>
            <span>Console<small>hs·iphone / internal</small></span>
        </a>

        <nav class="admin-nav">
            <span class="admin-nav-label">Menu utama</span>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">Ringkasan <span class="admin-nav-num">00</span></a>
            <a href="{{ route('admin.products') }}" class="{{ request()->routeIs('admin.products*') ? 'is-active' : '' }}">Produk <span class="admin-nav-num">01</span></a>
            <a href="{{ route('admin.sell-requests') }}" class="{{ request()->routeIs('admin.sell-requests*') ? 'is-active' : '' }}">Pengajuan Jual <span class="admin-nav-num">02</span></a>
            <a href="{{ route('admin.orders') }}" class="{{ request()->routeIs('admin.orders*') ? 'is-active' : '' }}">Pesanan <span class="admin-nav-num">03</span></a>
            <a href="{{ route('admin.proofs') }}" class="{{ request()->routeIs('admin.proofs*') ? 'is-active' : '' }}">Bukti Sold <span class="admin-nav-num">04</span></a>
            <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users*') ? 'is-active' : '' }}">Pengguna <span class="admin-nav-num">05</span></a>

            <span class="admin-nav-label">Area publik</span>
            <a href="{{ route('home') }}" target="_blank" rel="noopener">Lihat toko <span class="admin-nav-num">↗</span></a>
        </nav>

        <div class="admin-sidebar-foot">
            AKSES INTERNAL<br>Hanya untuk administrator HS·iPhone Depok.
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <h1>{{ $title ?? 'Admin' }}</h1>
            <div class="admin-topmeta">
                <span>{{ auth()->user()->name }}</span>
                <span class="admin-avatar">{{ auth()->user()->initials() }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="admin-logout">Keluar</button>
                </form>
            </div>
        </header>

        <div class="admin-content">
            <div class="admin-page">{{ $slot }}</div>
        </div>
    </div>
</div>

<div id="toast-container"></div>

<a href="{{ route('chat.index') }}" class="ed-chat-bubble" aria-label="Buka chat">
    <span class="ed-chat-bubble-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 9.6 9.6 0 0 1-3.5-.6L3 21l1.7-4.8A8.4 8.4 0 0 1 3.6 11.5 8.4 8.4 0 0 1 12 3.1a8.4 8.4 0 0 1 9 8.4Z"/>
        </svg>
    </span>
    <span class="ed-chat-bubble-label">Chat</span>
</a>
</body>
</html>
