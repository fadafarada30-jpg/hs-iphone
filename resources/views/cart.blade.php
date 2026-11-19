<x-layout title="Keranjang">
    <div class="cart-stage">
        <div class="ed-wrap">
            <div class="cart-head">
                <div>
                    <p class="cart-kicker">Keranjang / Checkout</p>
                    <h1 class="cart-title">Keranjang kamu.</h1>
                </div>
                @if($listings->isNotEmpty())
                    <span class="cart-count">{{ count($listings) }} unit dipilih</span>
                @endif
            </div>

            @if($listings->isEmpty())
                <div class="cart-empty">
                    <p class="cart-empty-mark">Belum ada unit</p>
                    <h3>Keranjang masih kosong.</h3>
                    <p>Simpan unit incaran kamu di sini, lalu lanjutkan ke checkout ketika sudah siap.</p>
                    <a href="{{ route('listings.index') }}" class="sell-submit">Jelajahi stok ready</a>
                </div>
            @else
                <div class="cart-layout">
                    <div class="cart-list">
                        @foreach($listings as $listing)
                            <article class="cart-item">
                                <div class="cart-thumb">
                                    <x-catalog-photo alt="{{ $listing->model }}" />
                                </div>

                                <div>
                                    <p class="cart-item-model">{{ $listing->model }}</p>
                                    <h2 class="cart-item-title">{{ $listing->storageLabel() }} · {{ $listing->color }}</h2>
                                    <p class="cart-item-meta">{{ $listing->condition->label() }} · BH {{ $listing->battery_health }}% · {{ $listing->location }}</p>
                                </div>

                                <div>
                                    <p class="cart-item-price">Rp {{ number_format($listing->price, 0, ',', '.') }}</p>
                                    <form action="{{ route('cart.remove', $listing) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="cart-remove">Hapus</button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <aside class="cart-summary">
                        <div class="cart-summary-head">
                            <h2>Ringkasan</h2>
                            <span>{{ count($listings) }} unit</span>
                        </div>

                        <div class="cart-lines">
                            @foreach($listings as $l)
                                <div class="cart-line">
                                    <span>{{ $l->model }} · {{ $l->storageLabel() }}</span>
                                    <span>Rp {{ number_format($l->price, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="cart-total">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($listings->sum('price'), 0, ',', '.') }}</span>
                        </div>

                        @auth
                            <a href="{{ route('checkout') }}" class="sell-submit">Lanjut checkout</a>
                        @else
                            <a href="{{ route('login') }}" class="sell-submit">Masuk untuk checkout</a>
                        @endauth

                        <p class="cart-note">Biaya pengiriman atau layanan COD akan dihitung pada langkah checkout. Unit disimpan sementara di keranjang ini.</p>
                    </aside>
                </div>
            @endif
        </div>
    </div>
</x-layout>
