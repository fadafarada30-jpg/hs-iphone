<x-driver-layout title="Jadwal COD">
    <div class="driver-page-head">
        <div>
            <p class="driver-kicker">Driver COD / Jadwal</p>
            <h1 class="driver-title">Jadwal COD saya.</h1>
            <p class="driver-subtitle">Daftar pesanan COD yang ditugaskan kepadamu. Absen keberangkatan dan unggah bukti penyerahan langsung dari jadwal ini.</p>
        </div>
    </div>

    @php
        $todayCount = $orders->filter(fn ($order) => $order->booking_date?->isToday())->count();
        $departedCount = $orders->filter(fn ($order) => $order->departed_at !== null)->count();
    @endphp

    <div class="driver-stats">
        <div class="driver-stat">
            <p class="driver-stat-num">{{ number_format($orders->count()) }}</p>
            <p class="driver-stat-label">Jadwal aktif</p>
            <p class="driver-stat-foot">Menunggu penyerahan unit</p>
        </div>
        <div class="driver-stat">
            <p class="driver-stat-num">{{ number_format($todayCount) }}</p>
            <p class="driver-stat-label">Jadwal hari ini</p>
            <p class="driver-stat-foot">Perlu diprioritaskan</p>
        </div>
        <div class="driver-stat">
            <p class="driver-stat-num">{{ number_format($departedCount) }}</p>
            <p class="driver-stat-label">Sudah berangkat</p>
            <p class="driver-stat-foot">Menunggu bukti sold</p>
        </div>
    </div>

    @if($orders->isEmpty())
        <div class="driver-empty">
            <p class="driver-empty-title">Belum ada jadwal COD aktif.</p>
            <p class="driver-empty-note">Jadwal baru akan muncul otomatis setelah customer menyelesaikan pesanan COD.</p>
        </div>
    @else
        <div class="driver-card-grid">
            @foreach($orders as $order)
                <article class="driver-card">
                    <div class="driver-card-head">
                        <div>
                            <div class="driver-card-title">{{ $order->code }}</div>
                            <div class="driver-card-meta">{{ $order->booking_date?->translatedFormat('d M Y') }} &middot; {{ $order->booking_time }} WIB</div>
                        </div>
                        <span class="badge badge-{{ $order->status->tone() }}">{{ $order->status->label() }}</span>
                    </div>

                    <div class="driver-card-body">
                        <div class="driver-meta-grid">
                            <div>
                                <p class="driver-meta-label">Customer</p>
                                <p class="driver-meta-value">{{ $order->user->name }}</p>
                            </div>
                            <div>
                                <p class="driver-meta-label">Telepon</p>
                                <p class="driver-meta-value">
                                    @if($order->recipient_phone)
                                        <a href="{{ $order->recipientPhoneLink() }}" class="driver-link-underline">{{ $order->recipient_phone }}</a>
                                    @else
                                        &mdash;
                                    @endif
                                </p>
                            </div>
                            <div class="driver-field full">
                                <p class="driver-meta-label">Alamat COD</p>
                                <p class="driver-meta-value">{{ $order->cod_address }}</p>
                            </div>
                            <div class="driver-field full">
                                <p class="driver-meta-label">Unit</p>
                                <p class="driver-meta-value">
                                    {{ $order->items->map(fn ($item) => $item->model.' '.$item->storage_gb.'GB '.$item->color)->join(', ') }}
                                </p>
                            </div>
                        </div>

                        <div class="driver-card-actions">
                            <a href="{{ route('driver.show', $order) }}" class="driver-btn">Lihat jadwal &amp; absen</a>
                            @if($order->recipient_phone)
                                <a href="{{ $order->recipientPhoneLink() }}" class="driver-btn driver-btn--ghost">Hubungi</a>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</x-driver-layout>
