<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Transfer = 'transfer';
    case Cod = 'cod';
    case Ewallet = 'ewallet';

    public function label(): string
    {
        return match ($this) {
            self::Transfer => 'Transfer bank',
            self::Cod => 'Bayar di tempat (COD)',
            self::Ewallet => 'E-wallet',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Transfer => 'BCA, Mandiri, BNI, BRI',
            self::Cod => 'Bayar saat unit tiba',
            self::Ewallet => 'GoPay, OVO, Dana',
        };
    }
}
