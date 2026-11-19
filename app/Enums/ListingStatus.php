<?php

namespace App\Enums;

enum ListingStatus: string
{
    case Active = 'active';
    case PendingReview = 'pending_review';
    case Sold = 'sold';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Aktif',
            self::PendingReview => 'Menunggu review',
            self::Sold => 'Terjual',
            self::Rejected => 'Ditolak',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::PendingReview => 'warning',
            self::Sold => 'neutral',
            self::Rejected => 'danger',
        };
    }
}
