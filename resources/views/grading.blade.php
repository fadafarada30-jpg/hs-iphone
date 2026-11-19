<x-layout title="Standar Garansi & Grading">
<section class="ed-page-hero">
    <div class="ed-wrap">
        <p class="ed-kicker-line">STANDAR TOKO HS · IPHONE DEPOK</p>
        <h1>Grade jelas.<br>Garansi jelas.</h1>
        <p class="ed-lede">
            Kami tidak memakai istilah “mulus” atau “bagus” saja. Setiap unit yang lolos QC masuk ke salah satu
            dari tiga grade di bawah ini. Kamu tahu persis apa yang kamu dapat sebelum membayar.
        </p>
    </div>
</section>

<section class="ed-section">
    <div class="ed-wrap">
        <p class="ed-kicker-line">01 DEFINISI GRADE</p>
        <h2 class="ed-whisper" style="margin-bottom:64px">Tiga standar, tanpa abu-abu.</h2>
        <div class="ed-grid-3">
            <article class="ed-grade">
                <h3>Grade A+</h3><small>KONDISI TERBAIK · HARGA TERTINGGI</small>
                <ul>
                    <li>Bodi tanpa goresan yang terlihat</li>
                    <li>Layar bebas dead pixel dan burn-in</li>
                    <li>Kesehatan baterai minimal 90%</li>
                    <li>Kelengkapan original utuh</li>
                    <li>Garansi tukar unit 7 hari</li>
                </ul>
            </article>
            <article class="ed-grade">
                <h3>Grade A</h3><small>SANGAT BAGUS · PALING BANYAK DIPILIH</small>
                <ul>
                    <li>Goresan halus mungkin ada, tak terlihat dari jauh</li>
                    <li>Layar normal, tanpa bintik mengganggu</li>
                    <li>Kesehatan baterai minimal 80%</li>
                    <li>Semua fungsi dan sensor berjalan normal</li>
                    <li>Garansi tukar unit 7 hari</li>
                </ul>
            </article>
            <article class="ed-grade">
                <h3>Grade B</h3><small>LAYAK PAKAI · PALING HEMAT</small>
                <ul>
                    <li>Goresan atau baret tipis masih terlihat</li>
                    <li>Layar tetap nyaman, tanpa cacat fatal</li>
                    <li>Kesehatan baterai minimal 70%</li>
                    <li>Semua fungsi utama tetap berfungsi</li>
                    <li>Garansi tukar unit 7 hari</li>
                </ul>
            </article>
        </div>

        <div class="ed-note">
            <strong>Catatan jujur:</strong> untuk Grade B kami tetap menjamin fungsi utama (layar, sinyal, kamera, baterai).
            Yang membedakan hanyalah kosmetik bodi. Jika ada kerusakan fungsi, unit tidak kami jual sebagai Grade B, melainkan masuk kategori unit service.
        </div>
    </div>
</section>

<section class="ed-section ed-section-dark">
    <div class="ed-wrap">
        <div class="ed-head-row">
            <div>
                <p class="ed-kicker-line">02 JAMINAN KAMI</p>
                <h2 class="ed-whisper">Garansi yang kamu dapat.</h2>
                <p class="ed-lede">Berlaku untuk semua grade di atas.</p>
            </div>
            <a href="{{ route('about') }}" class="ed-text-link">Kenapa kami bisa menjamin →</a>
        </div>

        <table class="ed-table">
            <thead><tr><th>Jenis garansi</th><th>Berlaku</th><th>Yang dicakup</th></tr></thead>
            <tbody>
                <tr><td><strong>Tukar unit</strong></td><td>7 hari sejak unit diterima</td><td>Kerusakan fungsi yang tidak sesuai deskripsi saat pembelian.</td></tr>
                <tr><td><strong>Garansi sinyal</strong></td><td>30 hari sejak unit diterima</td><td>Masalah SIM, Wi-Fi, atau sinyal yang bukan karena perlakuan pengguna.</td></tr>
                <tr><td><strong>Cek ulang gratis</strong></td><td>Kapan saja</td><td>Kamu bisa mampir ke store untuk dicek ulang bersama teknisi kami.</td></tr>
            </tbody>
        </table>

        <div class="ed-note" style="border-color:rgba(255,255,255,.5);color:rgba(255,255,255,.62)">
            Garansi tidak berlaku untuk kerusakan akibat jatuh, terkena air, modifikasi software, atau pemakaian di luar kondisi normal. Syarat lengkap diberikan bersama bukti pembelian.
        </div>
    </div>
</section>

<section class="ed-section">
    <div class="ed-wrap">
        <p class="ed-kicker-line">03 RINGKASAN PENGECEKAN</p>
        <h2 class="ed-whisper" style="margin-bottom:64px">Sama seperti di meja QC.</h2>
        <table class="ed-table">
            <thead><tr><th>Bagian</th><th>Yang dicek</th><th>Alat / cara</th></tr></thead>
            <tbody>
                <tr><td>Identitas</td><td>IMEI, serial, status iCloud</td><td>3uTools</td></tr>
                <tr><td>Layar</td><td>Dead pixel, burn-in, touch</td><td>Tes layar & sentuhan manual</td></tr>
                <tr><td>Baterai</td><td>Health, pengisian, port</td><td>3uTools & pengujian langsung</td></tr>
                <tr><td>Kamera</td><td>Lensa depan/belakang, flash, fokus</td><td>Tes foto & video</td></tr>
                <tr><td>Sinyal</td><td>SIM, Wi-Fi, Bluetooth, GPS</td><td>Tes konektivitas langsung</td></tr>
            </tbody>
        </table>
    </div>
</section>

<section class="ed-cta">
    <div class="ed-wrap ed-cta-row">
        <div>
            <p class="ed-kicker-line" style="color:rgba(255,255,255,.45)">SIAP PILIH?</p>
            <h2>Semua unit sudah lewat QC.</h2>
            <p>Pilih grade yang sesuai kebutuhanmu. Kalau ada yang kurang jelas, langsung chat admin kami.</p>
        </div>
        <a href="{{ route('listings.index') }}" class="ed-pill ed-pill-light">Lihat stok ready</a>
    </div>
</section>
</x-layout>
