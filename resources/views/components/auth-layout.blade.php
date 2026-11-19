<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'Masuk' }} · hs·iphone</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>H</text></svg>">
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
