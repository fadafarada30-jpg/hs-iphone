<x-admin-layout title="Tambah produk">
    <div class="admin-page-head">
        <div>
            <p class="admin-kicker">Katalog / Produk / Tambah</p>
            <h1 class="admin-title">Tambah produk.</h1>
            <p class="admin-subtitle">Buat listing baru langsung dari konsol internal. Unit akan tampil di katalog publik sesuai status yang dipilih.</p>
        </div>
        <div class="admin-actions">
            <a href="{{ route('admin.products') }}" class="admin-btn admin-btn--ghost admin-btn--sm">&larr; Kembali ke produk</a>
        </div>
    </div>

    @include('admin.products._form')
</x-admin-layout>
