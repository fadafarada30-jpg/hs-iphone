<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'Masuk' }} · hs·iphone</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23111111'/><text x='50' y='68' font-family='system-ui, -apple-system, sans-serif' font-weight='700' font-size='52' fill='%23ffffff' text-anchor='middle'>HS</text></svg>">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-shell">
@if(session('success'))
    <div data-flash="{{ session('success') }}" data-flash-type="success" hidden></div>
@endif
@if(session('error'))
    <div data-flash="{{ session('error') }}" data-flash-type="error" hidden></div>
@endif

{{ $slot }}

<div id="toast-container"></div>
@stack('scripts')
</body>
</html>
