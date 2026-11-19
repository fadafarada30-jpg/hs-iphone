<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' ' : '' }}hs·iphone</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23111111'/><text x='50' y='68' font-family='system-ui, -apple-system, sans-serif' font-weight='700' font-size='52' fill='%23ffffff' text-anchor='middle'>HS</text></svg>">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen {{ request()->routeIs('home') ? 'ed-body-hero' : '' }}" style="background:var(--color-canvas);color:var(--color-ink)">

{{-- Flash toasts rendered via JS --}}
@if(session('success'))
    <div data-flash="{{ session('success') }}" data-flash-type="success" hidden></div>
@endif
@if(session('error'))
    <div data-flash="{{ session('error') }}" data-flash-type="error" hidden></div>
@endif

{{-- Navbar --}}
@php $navHome = request()->routeIs('home'); @endphp
<nav class="ed-nav {{ $navHome ? 'ed-nav-overlay' : '' }}" id="ed-nav">
    <div class="ed-wrap ed-nav-inner">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="ed-logo ed-click" style="color:inherit">hs<span class="ed-logo-dot">·</span>iphone</a>

        {{-- Center locale-style label --}}
        <span class="ed-nav-place" aria-hidden="true"><i></i> DEPOK · ID</span>

        {{-- Desktop nav + actions --}}
        <div class="ed-nav-right">
        <div class="ed-nav-links">
            <a href="{{ route('listings.index') }}" class="ed-nav-link ed-click {{ request()->routeIs('listings.*') ? 'is-active' : '' }}">Stok Ready</a>
            <a href="{{ route('about') }}" class="ed-nav-link ed-click {{ request()->routeIs('about') ? 'is-active' : '' }}">Tentang Kami</a>
            <a href="{{ route('grading') }}" class="ed-nav-link ed-click {{ request()->routeIs('grading') ? 'is-active' : '' }}">Standar Garansi</a>
            <a href="{{ route('sell.create') }}" class="ed-nav-link ed-click {{ request()->routeIs('sell.*') ? 'is-active' : '' }}">Jual</a>
            @auth
                @if(auth()->user()->isDriver())
                    <a href="{{ route('driver.index') }}" class="ed-nav-link ed-click {{ request()->routeIs('driver.*') ? 'is-active' : '' }}">Jadwal COD</a>
                @endif
            @endauth
        </div>

        {{-- Icons + action --}}
        <div class="ed-nav-actions">
            <a href="{{ route('wishlist') }}" class="ed-nav-icon ed-click" aria-label="Wishlist">
                <svg width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.3" viewBox="0 0 24 24"><path d="M12 21l-1.45-1.32C5.4 15.36 2 12.27 2 8.5A5.5 5.5 0 0 1 7.5 3c1.74 0 3.41.81 4.5 2.08A5.99 5.99 0 0 1 16.5 3 5.5 5.5 0 0 1 22 8.5c0 3.77-3.4 6.86-8.55 11.19L12 21z"/></svg>
                @php $wCount = app(\App\Services\ShoppingSession::class)->wishlistCount(); @endphp
                @if($wCount > 0)<span class="ed-nav-count" data-wishlist-count>{{ $wCount }}</span>@endif
            </a>
            <a href="{{ route('cart') }}" class="ed-nav-icon ed-click" aria-label="Keranjang">
                <svg width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.3" viewBox="0 0 24 24"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                @php $cCount = app(\App\Services\ShoppingSession::class)->cartCount(); @endphp
                @if($cCount > 0)<span class="ed-nav-count" data-cart-count>{{ $cCount }}</span>@endif
            </a>
            @auth
                <a href="{{ route('account') }}" class="ed-nav-avatar ed-click" aria-label="Akun saya">{{ auth()->user()->initials() }}</a>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="ed-nav-console ed-click" aria-label="Konsol Admin">KONSOL</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="ed-nav-cta ed-click">Masuk</a>
            @endauth
            <button id="nav-menu-btn" class="ed-nav-burger" aria-label="Menu" aria-expanded="false" aria-controls="nav-mobile-menu">
                <span></span><span></span>
            </button>
        </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div id="nav-mobile-menu" class="ed-mobile-menu" aria-hidden="true">
        <div class="ed-wrap ed-mobile-menu-inner">
            <a href="{{ route('listings.index') }}" class="ed-click">Stok Ready <span>01</span></a>
            <a href="{{ route('about') }}" class="ed-click">Tentang Kami <span>02</span></a>
            <a href="{{ route('grading') }}" class="ed-click">Standar Garansi <span>03</span></a>
            <a href="{{ route('sell.create') }}" class="ed-click">Jual / Tukar Tambah <span>04</span></a>
            <a href="{{ route('wishlist') }}" class="ed-click">Wishlist <span>05</span></a>
            <a href="{{ route('cart') }}" class="ed-click">Keranjang <span>06</span></a>
            <a href="{{ route('orders.index') }}" class="ed-click">Pesanan Saya <span>07</span></a>
            @auth
                <a href="{{ route('account') }}" class="ed-click">Akun Saya <span>08</span></a>
                @if(auth()->user()->isDriver())
                    <a href="{{ route('driver.index') }}" class="ed-click">Jadwal COD <span>09</span></a>
                @endif
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="ed-click">Konsol Admin <span>10</span></a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="ed-mobile-logout">@csrf <button type="submit">Keluar</button></form>
            @else
                <a href="{{ route('login') }}" class="ed-click">Masuk <span>08</span></a>
            @endauth
        </div>
    </div>
</nav>

{{-- Main content --}}
<main>
    {{ $slot }}
</main>

{{-- Footer --}}
<footer class="ed-footer py-14">
    <div class="ed-wrap ed-footer-grid">
        <div>
            <h4>hs·iphone</h4>
            <p>Unit iPhone pilihan yang dicek 30 titik sebelum dijual.<br>Harga transparan · kondisi terverifikasi.</p>
        </div>
        <div>
            <h4>Explore</h4>
            <a href="{{ route('listings.index') }}">Stok Ready</a>
            <a href="{{ route('about') }}">Tentang Kami</a>
            <a href="{{ route('grading') }}">Standar Garansi</a>
            <a href="{{ route('sell.create') }}">Jual / Tukar Tambah</a>
        </div>
        <div>
            <h4>Showroom</h4>
            <p>Jl. Margonda Raya No. 123<br>Beji, Depok, Jawa Barat 16424</p>
            <p style="margin-top:8px">Senin – Sabtu · 10.00 – 19.00 WIB</p>
        </div>
    </div>
    <div class="ed-wrap ed-footer-bottom">© {{ date('Y') }} HS·IPHONE DEPOK. DIBUAT UNTUK TUGAS WEB PROGRAMMING.</div>
</footer>

<div id="toast-container"></div>

@php
    $chatUnread = auth()->check()
        ? \App\Models\ChatThread::where('buyer_id', auth()->id())->sum('unread_count')
        : 0;
@endphp
<a href="{{ auth()->check() ? route('chat.index') : route('login') }}" class="ed-chat-bubble" aria-label="Buka chat">
    <span class="ed-chat-bubble-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 9.6 9.6 0 0 1-3.5-.6L3 21l1.7-4.8A8.4 8.4 0 0 1 3.6 11.5 8.4 8.4 0 0 1 12 3.1a8.4 8.4 0 0 1 9 8.4Z"/>
        </svg>
    </span>
    <span class="ed-chat-bubble-label">{{ auth()->check() ? 'Chat toko' : 'Butuh bantuan?' }}</span>
    @if($chatUnread > 0)
        <span class="ed-chat-bubble-badge">{{ $chatUnread > 9 ? '9+' : $chatUnread }}</span>
    @endif
</a>
@stack('scripts')
</body>
</html>
