<x-admin-layout title="Edit produk">
    <div class="admin-page-head">
        <div>
            <p class="admin-kicker">Katalog / Produk / Edit</p>
            <h1 class="admin-title">Edit produk.</h1>
            <p class="admin-subtitle">Perbarui spesifikasi, harga, status, dan foto unit {{ $listing->model }}.</p>
        </div>
        <div class="admin-actions">
            <a href="{{ route('admin.products') }}" class="admin-btn admin-btn--ghost admin-btn--sm">&larr; Kembali ke produk</a>
        </div>
    </div>

    @include('admin.products._form')
</x-admin-layout>
