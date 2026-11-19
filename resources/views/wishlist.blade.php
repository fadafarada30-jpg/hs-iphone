<x-layout title="Wishlist">

<div class="container-apple py-10">
    <h1 class="section-title mb-6">Wishlist saya</h1>

    @if($listings->isEmpty())
        <div class="text-center py-24" style="color:var(--color-muted)">
            <p class="text-5xl mb-3">♡</p>
            <p class="font-semibold text-lg">Wishlist kosong</p>
            <p class="mt-1 text-sm">Simpan iPhone yang kamu suka dari halaman produk.</p>
            <a href="{{ route('listings.index') }}" class="btn btn-primary btn-sm mt-5">Jelajahi catalog</a>
        </div>
    @else
        <div class="listings-grid">
            @foreach($listings as $listing)
                <x-listing-card :listing="$listing" />
            @endforeach
        </div>
    @endif
</div>

</x-layout>
