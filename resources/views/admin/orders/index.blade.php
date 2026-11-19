<x-admin-layout title="Kelola pesanan">
    <div class="admin-page-head">
        <div>
            <p class="admin-kicker">Transaksi / Pesanan</p>
            <h1 class="admin-title">Pesanan masuk.</h1>
            <p class="admin-subtitle">Pantau alur pesanan pelanggan dan majukan statusnya sampai selesai.</p>
        </div>
        <div class="admin-actions">
            <a href="{{ route('admin.dashboard') }}" class="admin-btn admin-btn--ghost admin-btn--sm">Dashboard</a>
        </div>
    </div>

    <div class="admin-stats">
        <div class="admin-stat">
            <p class="admin-stat-num">{{ number_format($counts['total']) }}</p>
            <p class="admin-stat-label">Total pesanan</p>
            <p class="admin-stat-foot">Seluruh transaksi tercatat</p>
        </div>
        <div class="admin-stat">
            <p class="admin-stat-num">{{ number_format($counts['processing']) }}</p>
            <p class="admin-stat-label">Diproses</p>
            <p class="admin-stat-foot">Menunggu penyiapan unit</p>
        </div>
        <div class="admin-stat">
            <p class="admin-stat-num">{{ number_format($counts['shipped']) }}</p>
            <p class="admin-stat-label">Dikirim</p>
            <p class="admin-stat-foot">Dalam perjalanan / COD</p>
        </div>
        <div class="admin-stat">
            <p class="admin-stat-num">{{ number_format($counts['completed']) }}</p>
            <p class="admin-stat-label">Selesai</p>
            <p class="admin-stat-foot">Transaksi selesai</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.orders') }}" class="admin-filter">
        <select name="status" class="input">
            <option value="">Semua status</option>
            @foreach($statuses as $status)
                <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm">Filter</button>
        <span class="admin-filter-spacer"></span>
        <span class="admin-filter-count">{{ $orders->total() }} pesanan ditemukan</span>
    </form>

    @if($orders->isEmpty())
        <div class="admin-empty">Belum ada pesanan yang cocok dengan filter ini.</div>
    @else
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Kode pesanan</th>
                        <th>Pembeli</th>
                        <th class="num">Total</th>
                        <th class="center">Status</th>
                        <th class="actions">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>
                                <div class="admin-cell-title">{{ $order->code }}</div>
                                <div class="admin-cell-meta">{{ $order->created_at->translatedFormat('d M Y H:i') }} &middot; {{ count($order->items) }} unit</div>
                            </td>
                            <td>
                                <div class="admin-cell-title">{{ $order->user->name }}</div>
                                <div class="admin-cell-meta">{{ $order->shipping_method->label() }}</div>
                            </td>
                            <td class="num">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                            <td class="center">
                                <span class="admin-pill {{ $order->status->step() >= 2 ? 'is-strong' : '' }} {{ $order->status->value === 'cancelled' ? 'is-danger' : '' }}">
                                    {{ $order->status->label() }}
                                </span>
                            </td>
                            <td class="actions">
                                @if($order->status->step() >= 0 && $order->status->step() < 3)
                                    <form action="{{ route('admin.orders.advance', $order) }}" method="POST" class="admin-action-form">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm">Majukan</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="admin-filter" style="margin-top:18px; margin-bottom:0;">
            <span class="admin-filter-spacer"></span>
            {{ $orders->links() }}
        </div>
    @endif
</x-admin-layout>
