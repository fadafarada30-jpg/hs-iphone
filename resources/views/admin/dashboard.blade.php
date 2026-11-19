<x-admin-layout title="Ringkasan">
    <div class="admin-page-head">
        <div>
            <p class="admin-kicker">Konsol internal / Ringkasan</p>
            <h1 class="admin-title">Dashboard operasional.</h1>
            <p class="admin-subtitle">Pantau katalog, pengajuan jual, pesanan, dan bukti sold dari satu tempat.</p>
        </div>
        <div class="admin-actions">
            <a href="{{ route('admin.products.create') }}" class="admin-btn">Tambah produk</a>
            <a href="{{ route('admin.sell-requests') }}" class="admin-btn admin-btn--ghost">Pengajuan jual</a>
        </div>
    </div>

    <div class="admin-stats">
        <div class="admin-stat">
            <p class="admin-stat-num">{{ number_format($activeListings) }}</p>
            <p class="admin-stat-label">Listing aktif</p>
            <p class="admin-stat-foot">Dari {{ number_format($totalListings) }} total katalog</p>
        </div>
        <div class="admin-stat">
            <p class="admin-stat-num">{{ number_format($totalListings) }}</p>
            <p class="admin-stat-label">Total listing</p>
            <p class="admin-stat-foot">Termasuk unit terjual & ditolak</p>
        </div>
        <div class="admin-stat">
            <p class="admin-stat-num">{{ number_format($totalOrders) }}</p>
            <p class="admin-stat-label">Total pesanan</p>
            <p class="admin-stat-foot">Seluruh transaksi tercatat</p>
        </div>
        <div class="admin-stat">
            <p class="admin-stat-num">{{ number_format($totalUsers) }}</p>
            <p class="admin-stat-label">Pengguna</p>
            <p class="admin-stat-foot">Pembeli, admin, dan driver</p>
        </div>
    </div>

    <div class="admin-grid admin-grid--wide">
        <section class="admin-panel">
            <div class="admin-panel-head">
                <h2>Pesanan terbaru</h2>
                <a href="{{ route('admin.orders') }}" class="admin-link">Kelola semua</a>
            </div>
            <div class="admin-rows">
                @forelse($recentOrders as $order)
                    <div class="admin-row">
                        <div>
                            <div class="admin-row-lead">{{ $order->code }}</div>
                            <div class="admin-row-meta">{{ $order->user->name }} &middot; {{ $order->created_at->translatedFormat('d M Y') }}</div>
                        </div>
                        <span class="admin-row-value">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        <span class="admin-pill {{ $order->status->step() >= 2 ? 'is-strong' : '' }}">{{ $order->status->label() }}</span>
                    </div>
                @empty
                    <div class="admin-empty">Belum ada pesanan masuk.</div>
                @endforelse
            </div>
        </section>

        <section class="admin-panel">
            <div class="admin-panel-head">
                <h2>Listing terbaru</h2>
                <a href="{{ route('admin.products') }}" class="admin-link">Kelola semua</a>
            </div>
            <div class="admin-rows">
                @forelse($recentListings as $l)
                    <div class="admin-row">
                        <div>
                            <div class="admin-row-lead">{{ $l->model }}</div>
                            <div class="admin-row-meta">{{ $l->seller->name }} &middot; {{ $l->storageLabel() }}</div>
                        </div>
                        <span class="admin-row-value">Rp {{ number_format($l->price, 0, ',', '.') }}</span>
                        <span class="admin-pill {{ $l->status->value === 'active' ? 'is-strong' : 'is-quiet' }}">{{ $l->status->label() }}</span>
                    </div>
                @empty
                    <div class="admin-empty">Belum ada listing dibuat.</div>
                @endforelse
            </div>
        </section>
    </div>
</x-admin-layout>
