<?php

namespace App\Enums;

enum Role: string
{
    case Buyer = 'buyer';
    case Driver = 'driver';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Buyer => 'Pembeli',
            self::Driver => 'Driver COD',
            self::Admin => 'Admin',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Buyer => 'neutral',
            self::Driver => 'warning',
            self::Admin => 'dark',
        };
    }
}
