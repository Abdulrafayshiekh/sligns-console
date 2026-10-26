<?php

namespace App\Support;

class BrandColour
{
    public const ALIGNA_PINK = '#c90076';

    public static function cssVariables(?string $hex): string
    {
        $hex = self::normalise($hex);

        return sprintf('--company-brand: %s; --company-ink: %s; --company-text: %s', $hex, self::ink($hex), self::text($hex));
    }

    public static function ink(string $hex): string
    {
        return self::luminance($hex) > 0.179 ? '#000000' : '#ffffff';
    }

    public static function text(string $hex): string
    {
        return self::luminance($hex) > 0.183 ? sprintf('color-mix(in srgb, %s 55%%, #000)', $hex) : $hex;
    }

    private static function normalise(?string $hex): string
    {
        return is_string($hex) && preg_match('/^#[0-9a-f]{6}$/i', $hex) ? strtolower($hex) : self::ALIGNA_PINK;
    }

    private static function luminance(string $hex): float
    {
        [$r, $g, $b] = array_map(function (string $pair) {
            $channel = hexdec($pair) / 255;

            return $channel <= 0.03928 ? $channel / 12.92 : (($channel + 0.055) / 1.055) ** 2.4;
        }, str_split(substr($hex, 1), 2));

        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }
}
