<?php

namespace App\Enums;

enum ShippingMethod: string
{
    case Cod = 'cod';
    case Pickup = 'pickup';
    case Regular = 'regular';
    case Instant = 'instant';

    public function label(): string
    {
        return match ($this) {
            self::Cod => 'COD · bayar di tempat',
            self::Pickup => 'Datang ke toko',
            self::Regular => 'Reguler · 2–4 hari',
            self::Instant => 'Instan · tiba hari ini',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Cod => 'Driver datang ke alamatmu sesuai jadwal',
            self::Pickup => 'Ambil unit langsung di toko sesuai jadwal',
            self::Regular => 'Dikirim dari kota penjual',
            self::Instant => 'Khusus area yang sama',
        };
    }

    public function cost(): int
    {
        return match ($this) {
            self::Cod, self::Pickup => 0,
            self::Regular => 18_000,
            self::Instant => 32_000,
        };
    }

    public function isScheduled(): bool
    {
        return in_array($this, [self::Cod, self::Pickup], true);
    }
}
