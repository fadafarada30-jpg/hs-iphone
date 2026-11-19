<x-layout title="Pesanan saya">
    <div class="orders-stage">
        <div class="ed-wrap">
            <div class="orders-head">
                <div>
                    <p class="sell-kicker">Pesanan / Riwayat</p>
                    <h1 class="sell-title">Pesanan saya.</h1>
                </div>
                <p class="orders-meta">{{ $orders->total() }} pesanan tercatat</p>
            </div>

            @if($orders->isEmpty())
                <div class="order-empty">
                    <p>Belum ada aktivitas</p>
                    <h2>Riwayat pesanan masih kosong.</h2>
                    <p>Unit yang kamu pesan akan tampil di halaman ini lengkap dengan status dan detailnya.</p>
                    <a href="{{ route('listings.index') }}" class="sell-submit">Jelajahi stok ready</a>
                </div>
            @else
                <div class="order-index">
                    @foreach($orders as $order)
                        <a href="{{ route('orders.show', $order) }}" class="order-index-row">
                            <span class="order-index-code">{{ $order->code }}</span>
                            <span class="order-index-date">{{ $order->created_at->format('d M Y') }}</span>
                            <span class="order-index-items">
                                @foreach($order->items->take(2) as $item)
                                    <span>{{ $item->model }} {{ $item->storageLabel() }}</span>
                                @endforeach
                                @if($order->items->count() > 2)
                                    <span>+{{ $order->items->count() - 2 }} unit</span>
                                @endif
                            </span>
                            <span class="order-index-total">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                            <span class="order-index-status">
                                <span class="order-status {{ $order->status->step() >= 2 ? 'is-strong' : '' }}">{{ $order->status->label() }}</span>
                            </span>
                            <span class="order-index-arrow">&rarr;</span>
                        </a>
                    @endforeach
                </div>

                <div class="orders-pagination">{{ $orders->links() }}</div>
            @endif
        </div>
    </div>
</x-layout>
