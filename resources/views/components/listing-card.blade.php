@props(['listing'])

@php
    $savings = $listing->savings();
    $isWishlisted = app(\App\Services\ShoppingSession::class)->isWishlisted($listing->id);
@endphp

<a href="{{ route('listings.show', $listing) }}" class="listing-card card block group">
    {{-- Media --}}
    <div class="lc-media relative aspect-square overflow-hidden">
        <x-catalog-photo alt="{{ $listing->model }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.03]" />

        <div class="lc-badges absolute top-3 left-3 flex flex-col gap-1.5">
            @if($listing->is_featured)
                <span class="badge badge-accent">Featured</span>
            @endif
            @if($savings)
                <span class="badge badge-success">Hemat Rp {{ number_format($savings, 0, ',', '.') }}</span>
            @endif
        </div>

        <button
            type="button"
            data-wishlist-id="{{ $listing->id }}"
            aria-pressed="{{ $isWishlisted ? 'true' : 'false' }}"
            class="lc-wish absolute top-3 right-3 {{ $isWishlisted ? 'is-active' : '' }}"
            aria-label="Wishlist"
        >
            <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21l-1.45-1.32C5.4 15.36 2 12.27 2 8.5A5.5 5.5 0 0 1 7.5 3c1.74 0 3.41.81 4.5 2.08A5.99 5.99 0 0 1 16.5 3 5.5 5.5 0 0 1 22 8.5c0 3.77-3.4 6.86-8.55 11.19L12 21z"/></svg>
        </button>
    </div>

    {{-- Info --}}
    <div class="lc-info">
        <p class="lc-model">{{ $listing->model }}</p>
        <h3 class="lc-title">{{ $listing->storage_gb >= 1024 ? intdiv($listing->storage_gb, 1024).' TB' : $listing->storage_gb.' GB' }} · {{ $listing->color }}</h3>

        <div class="lc-specs">
            <span class="lc-tag lc-tag-grade">{{ $listing->condition->label() }}</span>
            <span class="lc-tag">BH {{ $listing->battery_health }}%</span>
        </div>

        <div class="lc-price-row">
            <span class="lc-price">Rp {{ number_format($listing->price, 0, ',', '.') }}</span>
            @if($listing->original_price)
                <span class="lc-price-old">Rp {{ number_format($listing->original_price, 0, ',', '.') }}</span>
            @endif
        </div>

        <p class="lc-meta">{{ $listing->location }} · {{ $listing->seller->name }}</p>
    </div>
</a>
