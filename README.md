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

Langkah:

1. Buat project Railway baru, lalu pilih **Deploy from GitHub repo** dan pilih `fadafarada30-jpg/hs-iphone`.
2. Tambahkan layanan PostgreSQL di project yang sama.
3. Di service `hs-iphone`, buka tab **Variables** dan isi:
   - `APP_NAME` = `HS-iPhone`
   - `APP_ENV` = `production`
   - `APP_KEY` = `base64:TVwbPazj9WjBFuUSe89XJmX69JfLr7gPcRU6tFMNgPU=`
   - `APP_DEBUG` = `false`
   - `APP_URL` = `https://hs-iphone-production.up.railway.app`
   - `DB_CONNECTION` = `pgsql`
   - `DATABASE_URL` = connection string PostgreSQL dari Railway/Neon
4. Buka tab **Settings**, pastikan healthcheck path = `/health`, lalu klik **Generate Domain**.
5. Railway akan menjalankan `migrate` dan seeding otomatis saat database masih kosong.

Entrypoint otomatis menyesuaikan Apache dengan env `PORT` yang diberikan Railway dan menerjemahkan `DATABASE_URL` ke `DB_URL` yang dipakai Laravel.

## Pengujian

```bash
php artisan test
```

Hasil terakhir: **43 test passed, 190 assertions**.
