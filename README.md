# HS-iPhone

Sistem informasi etalase editorial dan konsol manajemen untuk showroom iPhone terkurasi. Dibangun dengan Laravel 12, MySQL/PostgreSQL, dan antarmuka monokrom editorial.

**Demo publik (URL permanen):** https://hs-iphone.loca.lt

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

## Akses publik permanen

Aplikasi saat ini dipublikasikan di:

**https://hs-iphone.loca.lt**

Subdomain `hs-iphone` tetap, jadi tautannya tidak berubah setiap kali tunnel dijalankan ulang.

Di Windows, script `scripts/watch-public.ps1` bertugas menjaga MySQL, Laravel, dan tunnel tetap hidup. Script ini didaftarkan sebagai scheduled task `HS-iPhone Public Watchdog` yang berjalan saat login. Script `scripts/start-tunnel.cmd` dipakai untuk menjalankan localtunnel dengan subdomain tetap.

Jika komputer dimatikan atau internet putus, alamat tetap sama; begitu penjaga berjalan kembali, tunnel akan tersambung kembali ke subdomain yang sama.

## Deploy ke Railway (opsional)

Repository ini juga menyertakan `Dockerfile`, `render.yaml`, dan `docker/entrypoint.sh` untuk deploy ke platform container.

- Entrypoint otomatis memakai `DATABASE_URL` bila tersedia, atau SQLite bila tidak ada.
- Apache menyesuaikan port dari env `PORT`.
- URL aplikasi terdeteksi dari host request, jadi tidak perlu men-set `APP_URL`.

Healthcheck: `/health`.

## Pengujian

```bash
php artisan test
```

Hasil terakhir: **43 test passed, 190 assertions**.
