<?php

namespace App\Enums;

use App\Models\Company;

enum Theme: string
{
    case Aligna = 'aligna';
    case Night = 'night';
    case Blush = 'blush';
    case Atlas = 'atlas';
    case Company = 'company';

    public function label(): string
    {
        return match ($this) {
            self::Aligna => 'Aligna',
            self::Night => 'Aligna Night',
            self::Blush => 'Blush',
            self::Atlas => 'Atlas',
            self::Company => 'Company colours',
        };
    }

    public function isDark(): bool
    {
        return $this === self::Night;
    }

    public static function options(?Company $company): array
    {
        return array_values(array_filter(
            self::cases(),
            fn (self $theme) => $theme !== self::Company || $company !== null,
        ));
    }

    public static function resolve(self|string|null $value, ?Company $company): self
    {
        $theme = $value instanceof self ? $value : (self::tryFrom((string) $value) ?? self::Aligna);

        return $theme === self::Company && $company === null ? self::Aligna : $theme;
    }
}
