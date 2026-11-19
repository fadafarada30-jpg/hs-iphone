<?php

namespace App\Enums;

enum ListingCondition: string
{
    case LikeNew = 'like_new';
    case Excellent = 'excellent';
    case Good = 'good';
    case Fair = 'fair';

    public function label(): string
    {
        return match ($this) {
            self::LikeNew => 'Like new',
            self::Excellent => 'Excellent',
            self::Good => 'Good',
            self::Fair => 'Fair',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::LikeNew => 'success',
            self::Excellent => 'accent',
            self::Good => 'warning',
            self::Fair => 'danger',
        };
    }

    /**
     * Price factor applied to the base price of a model.
     */
    public function priceFactor(): float
    {
        return match ($this) {
            self::LikeNew => 1.0,
            self::Excellent => 0.92,
            self::Good => 0.84,
            self::Fair => 0.72,
        };
    }
}
