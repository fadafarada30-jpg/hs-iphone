<x-layout title="Beranda">
@php
    // TODO: ganti dengan alamat, jam buka, dan tautan Google Maps asli toko
    $storeAddress = 'Jl. Margonda Raya No. 123, Beji, Depok, Jawa Barat 16424';
    $mapsQuery = urlencode($storeAddress);
@endphp

{{-- ── HERO liquid light, silent typography ──────────────────────────── --}}
<section class="ed-hero">
    <div class="ed-hero-media" aria-hidden="true">
        <div class="ed-blob ed-blob-a"></div>
        <div class="ed-blob ed-blob-b"></div>
        <div class="ed-blob ed-blob-c"></div>
    </div>

    <div class="ed-wrap ed-hero-inner">
        <p class="ed-kicker">SHOWROOM iPHONE · DEPOK UNIT PILIHAN, CEK LANGSUNG</p>
        <h1 class="ed-display">Upgrade gadget<br><em>tanpa was-was.</em></h1>
        <p class="ed-hero-sub">
            <strong>100% lolos QC & garansi sinyal.</strong> Setiap unit dicek 30 titik oleh teknisi kami, meliputi
            layar, baterai, kamera, dan sinyal.
        </p>
        <div class="ed-hero-actions">
            <a href="{{ route('listings.index') }}" class="ed-pill ed-pill-light">Lihat Stok Ready</a>
            <a href="{{ route('sell.create') }}" class="ed-pill ed-pill-light">Jual / Tukar Tambah HP</a>
        </div>
    </div>

    <div class="ed-scroll" aria-hidden="true">
        <svg viewBox="0 0 100 100">
            <defs><path id="edScrollPath" d="M50,50 m-38,0 a38,38 0 1,1 76,0 a38,38 0 1,1 -76,0" /></defs>
            <text><textPath href="#edScrollPath">SCROLL TO EXPLORE · SCROLL TO EXPLORE · </textPath></text>
        </svg>
        <span class="ed-scroll-arrow">↓</span>
    </div>
    <div class="ed-hero-meta">30-POINT QC · 7 DAY EXCHANGE</div>
</section>

{{-- ── VALUE PROPOSITION ────────────────────────────────────────────────── --}}
<section class="ed-section">
    <div class="ed-wrap">
        <div class="ed-head-row">
            <div>
                <p class="ed-kicker-line">01 KENAPA KAMI</p>
                <h2 class="ed-whisper">Kenapa beli di HS Depok?</h2>
                <p class="ed-lede">Standar kami, bukan sekadar janji.</p>
            </div>
            <a href="{{ route('grading') }}" class="ed-text-link">Lihat standar garansi →</a>
        </div>

        <div class="ed-values">
            <article class="ed-value">
                <div class="ed-value-top">
                    <span class="ed-glyph"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M9 4h6M10 4v3M14 4v3M6 7h12v13H6z"/><path d="m9.5 13 2 2 3.5-4"/></svg></span>
                    <span class="ed-value-num">01 / 04</span>
                </div>
                <div class="ed-value-body">
                    <h3>30-Point Quality Control</h3>
                    <p>30 titik pengecekan sebelum unit masuk etalase. Tidak lolos, tidak kami jual.</p>
                </div>
            </article>

            <article class="ed-value">
                <div class="ed-value-top">
                    <span class="ed-glyph"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4.5 6v5.5c0 4.4 3.1 8.2 7.5 9.5 4.4-1.3 7.5-5.1 7.5-9.5V6L12 3Z"/><path d="M9.5 12.2 11.2 14l3.4-3.8"/></svg></span>
                    <span class="ed-value-num">02 / 04</span>
                </div>
                <div class="ed-value-body">
                    <h3>Garansi Tukar Unit 7 Hari</h3>
                    <p>Ada masalah di unit? Tukar dalam 7 hari. Garansi sinyal 30 hari tanpa ribet.</p>
                </div>
            </article>

            <article class="ed-value">
                <div class="ed-value-top">
                    <span class="ed-glyph"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10h13v7H3zM16 12h3l2 2.5V17h-5z"/><circle cx="7" cy="18" r="1.8"/><circle cx="18" cy="18" r="1.8"/><path d="M6 10V6h6v4"/></svg></span>
                    <span class="ed-value-num">03 / 04</span>
                </div>
                <div class="ed-value-body">
                    <h3>Bisa COD / Cek Langsung di Store</h3>
                    <p>Datang ke store untuk cek unit, atau pilih COD. Driver kami antar sampai tempatmu.</p>
                </div>
            </article>

            <article class="ed-value">
                <div class="ed-value-top">
                    <span class="ed-glyph"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18"/><path d="M17.5 7.5H9.8a3 3 0 0 0 0 6h4.4a3 3 0 0 1 0 6H6"/><circle cx="12" cy="12" r="9.2"/></svg></span>
                    <span class="ed-value-num">04 / 04</span>
                </div>
                <div class="ed-value-body">
                    <h3>Harga Jujur, Grade Jelas</h3>
                    <p>A+, A, atau B. Semua kami jelaskan apa bedanya sebelum kamu bayar.</p>
                </div>
            </article>
        </div>
    </div>
</section>

{{-- ── SERIES RAIL ──────────────────────────────────────────────────────── --}}


{{-- ── FEATURED ─────────────────────────────────────────────────────────── --}}
@if($featured->isNotEmpty())
<section class="ed-section ed-listings">
    <div class="ed-wrap">
        <div class="ed-head-row">
            <div>
                <p class="ed-kicker-line">02 PILIHAN TIM</p>
                <h2 class="ed-whisper">Unit Pilihan</h2>
            </div>
            <a href="{{ route('listings.index') }}" class="ed-text-link">Lihat semua →</a>
        </div>
        <div class="ed-rail">
            <div class="ed-rail-inner">
                <span class="ed-rail-label">Stok ready</span>
                <a href="{{ route('listings.index') }}" class="is-active">Semua unit</a>
                @foreach(['16','15','14','13','12','11'] as $s)
                    <a href="{{ route('listings.index', ['seri' => $s]) }}">iPhone {{ $s }} @isset($seriesCounts[$s])<span>{{ $seriesCounts[$s] }}</span>@endisset</a>
                @endforeach
            </div>
        </div>
        <div class="listings-grid">
            @foreach($featured as $listing)
                <x-listing-card :listing="$listing" />
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── SOCIAL PROOF monochrome band ──────────────────────────────────── --}}
<section class="ed-section ed-section-dark">
    <div class="ed-wrap">
        <div class="ed-head-row">
            <div>
                <p class="ed-kicker-line">03 BUKTI, BUKAN JANJI</p>
                <h2 class="ed-whisper">Kata pembeli, buktinya di sini.</h2>
            </div>
            <a href="{{ route('about') }}" class="ed-text-link">Tentang kami →</a>
        </div>

        {{-- TODO: ganti dengan tangkapan layar testimoni WhatsApp asli --}}
        <div class="ed-quotes">
            <article class="ed-quote">
                <p>“Unit iPhone 13-nya mulus banget, baterai 92% sesuai deskripsi. Bisa cek langsung di toko, pelayanan jujur.”</p>
                <small><span class="ed-stars">★★★★★</span><br>RANI A. · GOOGLE MAPS</small>
            </article>
            <article class="ed-quote">
                <p>“COD-nya cepat, drivernya ramah. HP sudah dicek 3uTools di depan saya. Recommended deh.”</p>
                <small><span class="ed-stars">★★★★★</span><br>DEDI P. · WHATSAPP</small>
            </article>
            <article class="ed-quote">
                <p>“Jual HP lama di sini gak ribet. Setelah diacc langsung dichat admin, harga negosiasi transparan.”</p>
                <small><span class="ed-stars">★★★★★</span><br>NADIA K. · WHATSAPP</small>
            </article>
        </div>

        <div class="ed-map">
            <iframe
                title="Ulasan Google Maps HS Depok"
                src="https://www.google.com/maps?q={{ $mapsQuery }}&output=embed"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
            <div class="ed-map-caption">RATING 4.9 · ULASAN PEMBELI DI GOOGLE MAPS</div>
        </div>
    </div>
</section>

{{-- ── STORE & HOURS ────────────────────────────────────────────────────── --}}
<section class="ed-section" id="lokasi">
    <div class="ed-wrap ed-store">
        <figure>
            {{-- Ilustrasi tampak depan toko. Ganti dengan foto asli saat tersedia --}}
            <svg viewBox="0 0 560 380" role="img" aria-label="Ilustrasi tampak depan toko HS Depok">
                <rect width="560" height="380" fill="#e8e8ed"/>
                <rect x="0" y="300" width="560" height="80" fill="#d2d2d7"/>
                <rect x="58" y="86" width="444" height="230" rx="0" fill="#f5f5f7" stroke="#8e8e93" stroke-width="2"/>
                <rect x="58" y="86" width="444" height="66" fill="#181818"/>
                <text x="280" y="128" text-anchor="middle" fill="#fff" font-family="Arial, sans-serif" font-size="27" font-weight="400" letter-spacing="2">HS·IPHONE</text>
                <rect x="86" y="176" width="200" height="140" fill="#b8d8f5" stroke="#8e8e93" stroke-width="3"/>
                <path d="M86 246h200M186 176v140" stroke="#f5f5f7" stroke-width="3"/>
                <rect x="316" y="176" width="150" height="140" fill="#fff" stroke="#8e8e93" stroke-width="3"/>
                <rect x="334" y="196" width="114" height="10" fill="#181818"/>
                <rect x="334" y="216" width="86" height="7" fill="#c7c7cc"/>
                <rect x="334" y="232" width="100" height="7" fill="#c7c7cc"/>
                <rect x="334" y="254" width="114" height="46" fill="#f5f5f7" stroke="#d2d2d7" stroke-width="2"/>
                <text x="391" y="282" text-anchor="middle" fill="#6d6d6d" font-family="Arial, sans-serif" font-size="11" letter-spacing="1">30-POINT QC</text>
                <rect x="30" y="300" width="500" height="10" fill="#a9a9ae"/>
            </svg>
        </figure>

        <div>
            <p class="ed-kicker-line">04 LOKASI & JAM OPERASIONAL</p>
            <h2 class="ed-whisper">Mampir, cek unitnya langsung.</h2>
            <p class="ed-lede">Lebih tenang kalau bisa pegang langsung HP-nya sebelum bayar.</p>

            <div class="ed-address">
                <strong>Alamat</strong>
                <p>{{ $storeAddress }}</p>
                <strong>Jam buka</strong>
                <p>Senin – Sabtu · 10.00 – 19.00 WIB<br>Minggu · 12.00 – 17.00 WIB</p>
                <strong>Cara ke sini</strong>
                <p>Akses mudah dari Margonda Raya. Parkir motor tersedia di depan toko.</p>
            </div>

            <a href="https://www.google.com/maps/search/?api=1&query={{ $mapsQuery }}" target="_blank" rel="noopener" class="ed-pill ed-pill-dark">Buka di Google Maps</a>
        </div>
    </div>
</section>

{{-- ── LATEST ───────────────────────────────────────────────────────────── --}}
<section class="ed-section ed-listings ed-section-line">
    <div class="ed-wrap">
        <div class="ed-head-row">
            <div>
                <p class="ed-kicker-line">05 BARU MASUK</p>
                <h2 class="ed-whisper">Listing terbaru</h2>
            </div>
            <a href="{{ route('listings.index') }}" class="ed-text-link">Lihat semua →</a>
        </div>
        <div class="listings-grid">
            @foreach($latest as $listing)
                <x-listing-card :listing="$listing" />
            @endforeach
        </div>
    </div>
</section>

{{-- ── CLOSING BAND ─────────────────────────────────────────────────────── --}}
<section class="ed-cta">
    <div class="ed-wrap ed-cta-row">
        <div>
            <p class="ed-kicker-line" style="color:rgba(255,255,255,.45)">JUAL / TUKAR TAMBAH</p>
            <h2>Punya iPhone untuk dijual?</h2>
            <p>Ajukan spesifikasinya. Tim HS Depok yang meninjau, lalu kamu kami hubungi lewat chat.</p>
        </div>
        <a href="{{ route('sell.create') }}" class="ed-pill ed-pill-light">Mulai jual sekarang</a>
    </div>
</section>

</x-layout>
