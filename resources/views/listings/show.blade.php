<x-layout :title="$listing->model.' '.$listing->storage_gb.'GB'">

<section class="ed-page-hero ed-detail-hero">
    <div class="ed-wrap">
        <p class="ed-kicker-line">Katalog HS · Unit Terkurasi</p>
        <div class="ed-detail-hero-row">
            <div>
                <h1>{{ $listing->model }}</h1>
                <p class="ed-lede">{{ $listing->storageLabel() }} · {{ $listing->color }}</p>
            </div>
            <div class="ed-detail-price">
                <span>Harga unit</span>
                <strong>Rp {{ number_format($listing->price,0,',','.') }}</strong>
                @if($listing->original_price)
                    <s>Harga baru Rp {{ number_format($listing->original_price,0,',','.') }}</s>
                    @if($listing->savings())
                        <em>Hemat Rp {{ number_format($listing->savings(),0,',','.') }}</em>
                    @endif
                @endif
            </div>
        </div>
        <div class="ed-detail-tags">
            <span class="ed-detail-tag">{{ $listing->condition->label() }}</span>
            <span class="ed-detail-tag">{{ $listing->warranty->label() }}</span>
            @if($listing->is_featured)
                <span class="ed-detail-tag">Featured</span>
            @endif
        </div>
    </div>
</section>

<section class="ed-section">
    <div class="ed-wrap">
        <div class="ed-grid-2 ed-detail-grid">
            <figure class="ed-detail-media">
                <x-catalog-photo alt="{{ $listing->model }}" class="ed-detail-photo" />
                <figcaption>Foto representatif · {{ $listing->model }} {{ $listing->storageLabel() }}</figcaption>
            </figure>

            <div class="ed-detail-side">
                <div class="ed-fact">
                    <h3>Spesifikasi unit</h3>
                    <table class="ed-table ed-detail-spec">
                        <tbody>
                            @foreach([
                                ['Kondisi', $listing->condition->label()],
                                ['Kapasitas baterai', $listing->battery_health.'%'],
                                ['Storage', $listing->storageLabel()],
                                ['Warna', $listing->color],
                                ['Garansi', $listing->warranty->label()],
                                ['Lokasi', $listing->location],
                            ] as [$label,$val])
                                <tr>
                                    <th>{{ $label }}</th>
                                    <td>{{ $val }}</td>
                                </tr>
                            @endforeach
                            @if($listing->completeness)
                                <tr>
                                    <th>Kelengkapan</th>
                                    <td>{{ implode(', ', $listing->completeness) }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="ed-detail-actions">
                    @if($listing->isAvailable())
                        <button type="button" data-cart-id="{{ $listing->id }}" class="ed-pill ed-pill-dark ed-detail-btn {{ $isInCart ? 'is-in-cart' : '' }}">
                            {{ $isInCart ? 'Di keranjang' : 'Tambah ke keranjang' }}
                        </button>
                        <button type="button" data-wishlist-id="{{ $listing->id }}" class="ed-pill ed-detail-btn {{ $isWishlisted ? 'is-active' : '' }}" aria-pressed="{{ $isWishlisted ? 'true' : 'false' }}">
                            {{ $isWishlisted ? 'Di wishlist' : 'Simpan ke wishlist' }}
                        </button>
                        @auth
                            <form action="{{ route('chat.start', $listing) }}" method="POST" class="ed-detail-chat-form">
                                @csrf
                                <button type="submit" class="ed-pill ed-detail-btn">Chat dengan penjual</button>
                            </form>
                        @endauth
                    @else
                        <p class="ed-detail-sold">Unit ini sudah terjual.</p>
                    @endif
                </div>

                <div class="ed-detail-seller">
                    <span class="ed-detail-avatar" style="background:{{ $listing->seller->avatar_color }}">{{ $listing->seller->initials() }}</span>
                    <div>
                        <small>Dijual oleh</small>
                        <strong>{{ $listing->seller->name }}</strong>
                        <p>{{ $listing->seller->city }} · {{ number_format($listing->seller->rating,1) }} ({{ $listing->seller->reviews_count }} ulasan)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="ed-section ed-section-line">
    <div class="ed-wrap ed-grid-2 ed-detail-split">
        <div>
            <p class="ed-kicker-line">Deskripsi unit</p>
            <h2 class="ed-detail-h2">Kondisi lengkap, tanpa keraguan.</h2>
        </div>
        <div class="ed-note ed-detail-desc">{{ $listing->description }}</div>
    </div>
</section>

@if($listing->reviews->isNotEmpty())
    <section class="ed-section">
        <div class="ed-wrap">
            <p class="ed-kicker-line">Ulasan pembeli</p>
            <h2 class="ed-detail-h2">Kata mereka.</h2>
            <div class="ed-detail-reviews">
                @foreach($listing->reviews->take(5) as $review)
                    <article class="ed-fact ed-detail-review">
                        <div class="ed-detail-review-head">
                            <span class="ed-detail-avatar ed-detail-avatar-sm" style="background:{{ $review->reviewer->avatar_color }}">{{ $review->reviewer->initials() }}</span>
                            <strong>{{ $review->reviewer->name }}</strong>
                            <span class="ed-detail-stars">{{ str_repeat('★', $review->rating) }}</span>
                        </div>
                        <p>{{ $review->body }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif

@if($bookings->isNotEmpty())
    <section class="ed-section ed-section-line">
        <div class="ed-wrap">
            <p class="ed-kicker-line">Jadwal booking</p>
            <h2 class="ed-detail-h2">Unit ini sudah dijadwalkan.</h2>
            <div class="ed-detail-bookings">
                @foreach($bookings as $booking)
                    <div class="ed-detail-booking">
                        <strong>{{ $booking->user->name }}</strong>
                        <span>{{ $booking->booking_date->translatedFormat('d M Y') }} · {{ $booking->booking_time }} WIB · {{ $booking->shipping_method->label() }}</span>
                    </div>
                @endforeach
            </div>
            <p class="ed-detail-fineprint">Alamat COD tidak ditampilkan demi privasi pembeli.</p>
        </div>
    </section>
@endif

@if($related->isNotEmpty())
    <section class="ed-section ed-section-line">
        <div class="ed-wrap">
            <p class="ed-kicker-line">Pilihan lain</p>
            <h2 class="ed-detail-h2">Produk serupa.</h2>
            <div class="listings-grid">
                @foreach($related as $item)
                    <x-listing-card :listing="$item" />
                @endforeach
            </div>
        </div>
    </section>
@endif

</x-layout>
