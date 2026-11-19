<x-admin-layout title="Kelola produk">
    <div class="admin-page-head">
        <div>
            <p class="admin-kicker">Katalog / Produk</p>
            <h1 class="admin-title">Kelola produk.</h1>
            <p class="admin-subtitle">
                Tinjau unit yang masuk, atur status katalog, dan tambahkan produk baru ke toko.
                @if($pendingCount > 0)
                    <strong>{{ $pendingCount }} unit</strong> menunggu review.
                @endif
            </p>
        </div>
        <div class="admin-actions">
            <a href="{{ route('admin.dashboard') }}" class="admin-btn admin-btn--ghost admin-btn--sm">Dashboard</a>
            <a href="{{ route('admin.products.create') }}" class="admin-btn">Tambah produk</a>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.products') }}" class="admin-filter">
        <input type="text" name="cari" value="{{ request('cari') }}" class="input" placeholder="Cari model iPhone.">
        <select name="status" class="input">
            <option value="">Semua status</option>
            @foreach($statuses as $s)
                <option value="{{ $s->value }}" @selected(request('status') === $s->value)>{{ $s->label() }}</option>
            @endforeach
        </select>
        <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm">Filter</button>
        <span class="admin-filter-spacer"></span>
        <span class="admin-filter-count">{{ $listings->total() }} produk ditemukan</span>
    </form>

    @if($listings->isEmpty())
        <div class="admin-empty">
            Belum ada produk yang cocok dengan filter ini.
        </div>
    @else
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Model</th>
                        <th>Spesifikasi</th>
                        <th class="num">Harga</th>
                        <th class="center">Status</th>
                        <th class="actions">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($listings as $l)
                        <tr>
                            <td>
                                <div class="admin-cell-title">{{ $l->model }}</div>
                                <div class="admin-cell-meta">Oleh {{ $l->seller->name }}</div>
                            </td>
                            <td>
                                <div class="admin-cell-title">{{ $l->storageLabel() }}</div>
                                <div class="admin-cell-meta">{{ $l->color }} &middot; {{ $l->condition->label() }} &middot; BH {{ $l->battery_health }}%</div>
                            </td>
                            <td class="num">Rp {{ number_format($l->price, 0, ',', '.') }}</td>
                            <td class="center">
                                <span class="admin-pill {{ $l->status->value === 'active' ? 'is-strong' : '' }} {{ $l->status->value === 'rejected' ? 'is-danger' : '' }} {{ $l->status->value === 'sold' ? 'is-quiet' : '' }}">
                                    {{ $l->status->label() }}
                                </span>
                            </td>
                            <td class="actions">
                                <div class="admin-actions-inline">
                                    <a href="{{ route('admin.products.edit', $l) }}" class="admin-action-link">Edit</a>
                                    @if($l->status->value === 'pending_review')
                                        <form action="{{ route('admin.products.approve', $l) }}" method="POST" class="admin-action-form">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="admin-action-link">Setujui</button>
                                        </form>
                                        <form action="{{ route('admin.products.reject', $l) }}" method="POST" class="admin-action-form">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="admin-action-link is-danger">Tolak</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.products.destroy', $l) }}" method="POST" class="admin-action-form" onsubmit="return confirm('Hapus listing ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="admin-action-link is-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="admin-filter" style="margin-top:18px; margin-bottom:0;">
            <span class="admin-filter-spacer"></span>
            {{ $listings->links() }}
        </div>
    @endif
</x-admin-layout>
