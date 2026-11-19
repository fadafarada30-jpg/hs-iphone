<?php

namespace App\Enums;

enum Warranty: string
{
    case Official = 'official';
    case Inter = 'inter';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Official => 'Garansi resmi',
            self::Inter => 'Garansi inter',
            self::Expired => 'Garansi habis',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::Official => 'Resmi',
            self::Inter => 'Inter',
            self::Expired => 'Habis',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Official => 'success',
            self::Inter => 'accent',
            self::Expired => 'neutral',
        };
    }
}
