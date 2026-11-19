@props(['color' => 'Space Black'])

@php
    $fill = match(true) {
        str_contains(strtolower($color), 'black') || str_contains(strtolower($color), 'midnight') => '#1d1d1f',
        str_contains(strtolower($color), 'gold') => '#c8a96e',
        str_contains(strtolower($color), 'silver') || str_contains(strtolower($color), 'starlight') || str_contains(strtolower($color), 'white') => '#e8e8ed',
        str_contains(strtolower($color), 'blue') || str_contains(strtolower($color), 'ultramarine') => '#0071e3',
        str_contains(strtolower($color), 'green') || str_contains(strtolower($color), 'teal') => '#34c759',
        str_contains(strtolower($color), 'purple') => '#af52de',
        str_contains(strtolower($color), 'pink') => '#ff2d55',
        str_contains(strtolower($color), 'red') || str_contains(strtolower($color), 'product') => '#ff3b30',
        str_contains(strtolower($color), 'desert') || str_contains(strtolower($color), 'natural') => '#c8b89a',
        str_contains(strtolower($color), 'titanium') => '#8e8e93',
        default => '#636366',
    };
@endphp

<svg width="80" height="120" viewBox="0 0 80 120" fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect x="8" y="2" width="64" height="116" rx="14" fill="{{ $fill }}" opacity=".9"/>
    <rect x="12" y="6" width="56" height="108" rx="11" fill="#000" opacity=".35"/>
    <rect x="14" y="8" width="52" height="104" rx="9.5" fill="{{ $fill }}" opacity=".6"/>
    {{-- Screen --}}
    <rect x="16" y="14" width="48" height="88" rx="7" fill="#1c1c1e"/>
    {{-- Dynamic island --}}
    <rect x="28" y="17" width="24" height="8" rx="4" fill="#000"/>
    {{-- Home indicator --}}
    <rect x="30" y="108" width="20" height="3" rx="1.5" fill="#fff" opacity=".3"/>
</svg>
