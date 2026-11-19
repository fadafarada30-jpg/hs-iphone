<x-layout title="Tentang Kami">
<section class="ed-page-hero">
    <div class="ed-wrap">
        <p class="ed-kicker-line">HS · IPHONE DEPOK TENTANG KAMI</p>
        <h1>Kami cek dulu,<br>baru kamu pakai.</h1>
        <p class="ed-lede">
            HS·iPhone berawal dari kegelisahan melihat orang kecewa beli HP second: kondisi tidak sesuai deskripsi,
            baterai cepat habis, sinyal bermasalah. Kami memutuskan bikin toko yang mengecek semuanya dulu
            dengan standar yang jelas, bukan cuma kata "mulus".
        </p>
    </div>
</section>

<section class="ed-section">
    <div class="ed-wrap">
        <p class="ed-kicker-line">01 PERJALANAN</p>
        <h2 class="ed-whisper">Perjalanan usaha kami</h2>
        <div class="ed-timeline">
            <div class="ed-timeline-item">
                <time>2020</time>
                <div><h3>Mulai dari meja service</h3><p>Dari satu meja di Depok, kami menerima HP untuk dibersihkan, ganti baterai, dan dicek sinyalnya.</p></div>
            </div>
            <div class="ed-timeline-item">
                <time>2022</time>
                <div><h3>Fokus ke iPhone pilihan</h3><p>Kami mulai membeli unit langsung dari pemilik, lalu menjualnya kembali setelah pengecekan menyeluruh.</p></div>
            </div>
            <div class="ed-timeline-item">
                <time>2024</time>
                <div><h3>Standar QC 30 titik</h3><p>Pengecekan ditetapkan jadi prosedur tetap: 3uTools, layar, baterai, kamera, dan sinyal. Grade A+, A, B mulai kami pakai.</p></div>
            </div>
            <div class="ed-timeline-item">
                <time>SEKARANG</time>
                <div><h3>Bisa COD & cek di store</h3><p>Showroom di Depok, layanan COD, dan chat langsung dengan admin. Tujuan kami tetap sama: beli HP jadi tidak perlu was-was.</p></div>
            </div>
        </div>
    </div>
</section>

<section class="ed-section ed-section-dark">
    <div class="ed-wrap">
        <p class="ed-kicker-line">02 STANDAR PENGECEKAN</p>
        <h2 class="ed-whisper">Apa yang kami cek.</h2>
        <p class="ed-lede" style="margin-bottom:64px">Ini yang kami lakukan sebelum sebuah unit boleh masuk etalase.</p>

        <div class="ed-grid-2">
            <div class="ed-fact"><h3>3uTools & identitas unit</h3><p>IMEI dan serial dicek, riwayat aktivasi dan status iCloud dipastikan aman.</p></div>
            <div class="ed-fact"><h3>Layar & touchscreen</h3><p>Dead pixel, burn-in, dan sensitivitas sentuh diuji di seluruh area layar.</p></div>
            <div class="ed-fact"><h3>Baterai & daya tahan</h3><p>Kesehatan baterai diukur, pengisian daya dan port charging dicoba langsung.</p></div>
            <div class="ed-fact"><h3>Kamera & sensor</h3><p>Semua lensa, flash, autofocus, Face ID, dan sensor diuji satu per satu.</p></div>
            <div class="ed-fact"><h3>Sinyal & konektivitas</h3><p>SIM, Wi-Fi, Bluetooth, GPS, dan speaker dites langsung. Garansi sinyal kami berikan.</p></div>
            <div class="ed-fact"><h3>Bodi & kelengkapan</h3><p>Kondisi bodi dicatat jujur, lalu difoto sebagai bukti sebelum diserahkan.</p></div>
        </div>

        <div class="ed-note" style="border-color:rgba(255,255,255,.5);color:rgba(255,255,255,.62)">
            Unit yang tidak lolos salah satu titik tidak kami jual sebagai unit layak pakai. Semua hasil pengecekan kami sampaikan sebelum kamu memutuskan beli.
        </div>
    </div>
</section>

<section class="ed-section">
    <div class="ed-wrap">
        <div class="ed-head-row">
            <div>
                <p class="ed-kicker-line">03 ORANG DI BALIKNYA</p>
                <h2 class="ed-whisper">Kenalan dengan tim kami</h2>
            </div>
            <a href="{{ route('grading') }}" class="ed-text-link">Lihat grade & garansi →</a>
        </div>

        <div class="ed-grid-3">
            <article class="ed-team">
                <h3>Dimas</h3><small>Founder & Teknisi QC</small>
                <p>Yang menentukan sebuah unit lolos atau tidak. Lebih dari 8 tahun pegang iPhone.</p>
            </article>
            <article class="ed-team">
                <h3>Admin HS Depok</h3><small>Customer Care</small>
                <p>Yang menjawab chat, menjelaskan grade, dan menemani proses tukar tambah dari awal.</p>
            </article>
            <article class="ed-team">
                <h3>Rizky</h3><small>Driver COD</small>
                <p>Yang mengantar unit sampai tujuan dan mengirimkan bukti serah terima.</p>
            </article>
        </div>
        <p style="font-size:11px;color:var(--ed-gray);margin-top:40px">Nama tim di atas adalah contoh untuk kebutuhan demo. Ganti sesuai data asli toko.</p>
    </div>
</section>

<section class="ed-cta">
    <div class="ed-wrap ed-cta-row">
        <div>
            <p class="ed-kicker-line" style="color:rgba(255,255,255,.45)">TRANSPARAN DARI AWAL</p>
            <h2>Mau lihat unitnya langsung?</h2>
            <p>Mampir ke showroom Depok, atau cek stok yang sudah lolos QC kami dulu dari rumah.</p>
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap">
            <a href="{{ route('listings.index') }}" class="ed-pill ed-pill-light">Lihat stok ready</a>
            <a href="{{ route('home') }}#lokasi" class="ed-pill ed-pill-light">Lokasi toko</a>
        </div>
    </div>
</section>
</x-layout>
