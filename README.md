# HS-iPhone

Sistem informasi etalase editorial dan konsol manajemen untuk showroom iPhone terkurasi. Dibangun dengan Laravel 12, MySQL/PostgreSQL, dan antarmuka monokrom editorial.

## Fitur utama

- Etalase publik dengan filter seri, kapasitas, warna, dan grade kondisi.
- Halaman detail produk dengan spesifikasi lengkap dan status garansi.
- Keranjang, wishlist, checkout, COD, dan store pickup.
- Chat realtime berbasis polling dengan indikator mengetik dan sinkronisasi WIB.
- Konsol admin untuk produk, pesanan, pengajuan jual, bukti sold, dan pengguna.
- Ruang kerja driver COD terpisah di `/driver`.
- Desain responsif dengan gaya editorial monokrom.

## Akun demo

| Role | Email | Password |
|---|---|---|
| Pembeli | `budi@demo.com` | `password` |
| Admin | `admin@demo.com` | `password` |
| Driver COD | `driver@demo.com` | `password` |

## Menjalankan secara lokal

```bash
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Deploy ke Render ( gratis, URL permanen )

Repository ini sudah menyertakan `Dockerfile`, `render.yaml`, dan entrypoint otomatis yang menjalankan migrasi serta seeding saat database masih kosong.

[![Deploy to Render](https://render.com/images/deploy-to-render-button.svg)](https://render.com/deploy?repo=https://github.com/fadafarada30-jpg/hs-iphone)

Langkah:

1. Klik tombol **Deploy to Render** di atas dan login dengan akun GitHub/Render Anda.
2. Render akan mendeteksi `render.yaml`, membuat web service `hs-iphone` dan database PostgreSQL `hs-iphone-db`.
3. Setelah selesai, buka URL yang diberikan Render, biasanya `https://hs-iphone.onrender.com`.
4. Jika nama service berubah, perbarui environment variable `APP_URL` di dashboard Render dengan URL aktual.

Catatan: paket gratis Render akan menghentikan web service setelah approximately 15 menit tanpa aktivitas, sehingga request pertama setelah itu membutuhkan beberapa detik untuk bangun. Alamat URL-nya tetap sama.

## Pengujian

```bash
php artisan test
```

Hasil terakhir: **43 test passed, 190 assertions**.
