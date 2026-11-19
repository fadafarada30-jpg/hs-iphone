# HS-iPhone

Sistem informasi etalase editorial dan konsol manajemen untuk showroom iPhone terkurasi. Dibangun dengan Laravel 12, MySQL/PostgreSQL, dan antarmuka monokrom editorial.

**Demo publik (URL permanen):** https://hs-iphone-production.up.railway.app

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

## Deploy ke Railway (URL permanen)

Repository ini menyertakan `Dockerfile` dan `docker/entrypoint.sh` sehingga Railway dapat langsung membangun dan menjalankan aplikasi.

Langkah paling singkat:

1. Buat project Railway baru, lalu pilih **Deploy from GitHub repo** dan pilih `fadafarada30-jpg/hs-iphone`.
2. Klik **Generate Domain** pada service `hs-iphone`.
3. Selesai. Aplikasi memakai SQLite otomatis, menjalankan migrasi + seeding saat database masih kosong, dan URL aplikasi terdeteksi dari host request. Tidak perlu menambahkan `DATABASE_URL`, `DB_CONNECTION`, atau `APP_URL`.

Jika ingin memakai PostgreSQL eksternal (opsional), tambahkan variabel `DATABASE_URL` dengan connection string-nya. Entrypoint akan otomatis memakai driver `pgsql`; variabel lama seperti `DB_HOST` tidak diperlukan.

Entrypoint juga menyesuaikan Apache dengan env `PORT` yang diberikan Railway.

## Pengujian

```bash
php artisan test
```

Hasil terakhir: **43 test passed, 190 assertions**.
