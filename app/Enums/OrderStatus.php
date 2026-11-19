<?php

namespace App\Enums;

enum OrderStatus: string
{
    case AwaitingPayment = 'awaiting_payment';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::AwaitingPayment => 'Menunggu pembayaran',
            self::Processing => 'Diproses',
            self::Shipped => 'Dikirim',
            self::Completed => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::AwaitingPayment => 'warning',
            self::Processing => 'accent',
            self::Shipped => 'accent',
            self::Completed => 'success',
            self::Cancelled => 'danger',
        };
    }

    /**
     * Position of the status inside the customer-facing timeline.
     */
    public function step(): int
    {
        return match ($this) {
            self::AwaitingPayment => 0,
            self::Processing => 1,
            self::Shipped => 2,
            self::Completed => 3,
            self::Cancelled => -1,
        };
    }
}
