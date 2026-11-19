<x-layout :title="'Pesanan '.$order->code">
    <div class="orders-stage">
        <div class="ed-wrap">
            <a href="{{ route('orders.index') }}" class="order-back">&larr; Pesanan saya</a>

            <div class="order-detail-head">
                <div>
                    <p class="sell-kicker">Detail pesanan</p>
                    <h1 class="order-detail-code">{{ $order->code }}</h1>
                    <p class="order-detail-date">{{ $order->created_at->format('d M Y, H:i') }} WIB</p>
                </div>
                <span class="order-status {{ $order->status->step() >= 2 ? 'is-strong' : '' }}">{{ $order->status->label() }}</span>
            </div>

            @php $step = $order->status->step(); @endphp
            @if($step >= 0)
                <div class="order-timeline">
                    @foreach([0 => 'Bayar', 1 => 'Proses', 2 => 'Kirim', 3 => 'Selesai'] as $i => $label)
                        <div class="order-step {{ $step > $i ? 'is-done' : '' }} {{ $step === $i ? 'is-active' : '' }}">
                            <div class="order-step-circle">{{ $i + 1 }}</div>
                            <div class="order-step-label">{{ $label }}</div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="order-detail-layout">
                <div>
                    {{-- Items --}}
                    <section class="order-section">
                        <div class="order-section-head">
                            <h2>Item pesanan</h2>
                            <span>{{ count($order->items) }} unit</span>
                        </div>
                        @foreach($order->items as $item)
                            <div class="order-item">
                                <div class="order-thumb">
                                    <x-catalog-photo alt="{{ $item->model }}" />
                                </div>
                                <div>
                                    <p class="order-item-model">{{ $item->model }}</p>
                                    <p class="order-item-meta">{{ $item->storageLabel() }} &middot; {{ $item->color }}</p>
                                    <p class="order-item-meta">Penjual: {{ $item->seller->name }}</p>
                                </div>
                                <p class="order-item-price">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </section>

                    {{-- Delivery info --}}
                    <section class="order-section">
                        <div class="order-section-head">
                            <h2>Info pengiriman</h2>
                            <span>{{ $order->shipping_method->label() }}</span>
                        </div>
                        <dl class="order-info">
                            <dt>Penerima</dt>
                            <dd>{{ $order->recipient_name }}</dd>
                            <dt>Telepon</dt>
                            <dd>{{ $order->recipient_phone }}</dd>
                            <dt>Kota</dt>
                            <dd>{{ $order->shipping_city }}</dd>
                            <dt>Alamat</dt>
                            <dd>{{ $order->shipping_address }}</dd>
                            <dt>Pembayaran</dt>
                            <dd>{{ $order->payment_method->label() }}</dd>
                        </dl>
                        @if($order->notes)
                            <p class="order-note">Catatan: {{ $order->notes }}</p>
                        @endif
                    </section>

                    {{-- Booking schedule --}}
                    @if($order->hasSchedule())
                        <section class="order-section">
                            <div class="order-section-head">
                                <h2>Jadwal {{ $order->isCod() ? 'COD' : 'ambil di toko' }}</h2>
                                <span>{{ $order->isCod() ? 'COD' : 'Ambil' }}</span>
                            </div>
                            <dl class="order-info">
                                <dt>Tanggal &amp; jam</dt>
                                <dd>{{ $order->booking_date?->translatedFormat('d M Y') }} &middot; {{ $order->booking_time }} WIB</dd>
                                @if($order->isCod())
                                    <dt>Alamat COD</dt>
                                    <dd>{{ $order->cod_address }}</dd>
                                    <dt>Driver</dt>
                                    <dd>{{ $order->driver?->name ?? 'Menunggu penugasan' }}</dd>
                                @endif
                                @if($order->departed_at)
                                    <dt>Driver berangkat</dt>
                                    <dd>{{ $order->departed_at->translatedFormat('d M Y H:i') }} WIB</dd>
                                    @if($order->departure_notes)
                                        <dt>Keterangan</dt>
                                        <dd>{{ $order->departure_notes }}</dd>
                                    @endif
                                @endif
                                <dt>Status bukti sold</dt>
                                <dd>
                                    <span class="order-status {{ $order->sold_proof_status->value === 'approved' ? 'is-strong' : '' }}">
                                        {{ $order->sold_proof_status->label() }}
                                    </span>
                                </dd>
                            </dl>
                            @if($order->sold_proof_status === \App\Enums\ProofStatus::Rejected && $order->sold_proof_rejected_note)
                                <p class="order-proof-note">Catatan admin: {{ $order->sold_proof_rejected_note }}</p>
                            @endif
                        </section>
                    @endif
                </div>

                {{-- Summary --}}
                <aside class="order-summary">
                    <div class="order-summary-head">
                        <h2>Ringkasan</h2>
                        <span>{{ $order->code }}</span>
                    </div>

                    <div class="order-summary-line">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="order-summary-line">
                        <span>Ongkir ({{ $order->shipping_method->label() }})</span>
                        <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="order-summary-total">
                        <span>Total</span>
                        <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>

                    @can('cancel', $order)
                        <form action="{{ route('orders.cancel', $order) }}" method="POST" class="order-cancel" onsubmit="return confirm('Batalkan pesanan ini?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit">Batalkan pesanan</button>
                        </form>
                    @endcan
                </aside>
            </div>
        </div>
    </div>
</x-layout>
